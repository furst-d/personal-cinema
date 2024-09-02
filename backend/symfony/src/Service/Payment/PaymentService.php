<?php

namespace App\Service\Payment;

use App\Entity\Account\Account;
use App\Entity\Storage\StorageUpgradePrice;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Exception\PaymentRequiredException;
use App\Helper\Storage\StoragePaymentMetadata;
use App\Helper\Storage\StoragePaymentType;
use App\Service\Account\AccountService;
use Stripe\Charge;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\Stripe;
use Stripe\StripeObject;

class PaymentService
{
    /**
     * @var string $frontendUrl
     */
    private string $frontendUrl;

    /**
     * @var AccountService $accountService
     */
    private AccountService $accountService;

    public const PAYMENT_NOT_COMPLETED_MESSAGE = 'Payment not completed';

    /**
     * @param string $stripeSecretKey
     * @param string $frontendUrl
     * @param AccountService $accountService
     */
    public function __construct(
        string $stripeSecretKey,
        string $frontendUrl,
        AccountService $accountService
    )
    {
        Stripe::setApiKey($stripeSecretKey);
        $this->frontendUrl = $frontendUrl;
        $this->accountService = $accountService;
    }

    /**
     * @param Account $account
     * @param StorageUpgradePrice $price
     * @return Session
     * @throws InternalException
     */
    public function createCheckoutSession(Account $account, StorageUpgradePrice $price): Session
    {
        try {
            return Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'czk',
                        'product_data' => [
                            'name' => "Soukromé kino - navýšení úložiště o {$price->getSizeInGB()} GB",
                        ],
                        'unit_amount' => $price->getDiscountedPriceCzk() * 100, // amount in cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => "$this->frontendUrl/storage?payment=success&session_id={CHECKOUT_SESSION_ID}",
                'cancel_url' => "$this->frontendUrl/storage?payment=failure",
                'metadata' => [
                    'user_id' => $account->getId(),
                    'price_czk' => $price->getDiscountedPriceCzk(),
                    'size' => $price->getSize(),
                ],
            ]);
        } catch (ApiErrorException) {
            throw new InternalException('Failed to create checkout session');
        }
    }

    /**
     * @param string $paymentIntent
     * @return void
     * @throws InternalException
     */
    public function cancelPayment(string $paymentIntent): void
    {
        try {
            Refund::create([
                'payment_intent' => $paymentIntent,
            ]);
        } catch (ApiErrorException) {
            throw new InternalException('Failed to cancel checkout session');
        }
    }

    /**
     * @param string $paymentIntent
     * @return bool
     * @throws InternalException
     */
    public function isRefunded(string $paymentIntent): bool
    {
        try {
            $paymentIntentObject = PaymentIntent::retrieve($paymentIntent);

            $lastCharge = $paymentIntentObject->latest_charge;

            if (is_string($lastCharge)) {
                $lastCharge = Charge::retrieve($lastCharge);
            }

            return $lastCharge->refunded;
        } catch (ApiErrorException $e) {
            throw new InternalException('Failed to check payment status: ' . $e->getMessage());
        }
    }

    /**
     * @param string $sessionId
     * @return Session
     * @throws InternalException|PaymentRequiredException
     */
    public function validatePayment(string $sessionId): Session
    {
        try {
            $session = Session::retrieve($sessionId);

            if ($session->payment_status !== 'paid') {
                throw new PaymentRequiredException(self::PAYMENT_NOT_COMPLETED_MESSAGE);
            }

            return $session;

        } catch (ApiErrorException) {
            throw new InternalException('Failed to retrieve checkout session');
        }
    }

    /**
     * @param StripeObject|null $metadata
     * @return StoragePaymentMetadata
     * @throws InternalException
     * @throws NotFoundException
     */
    public function validateMetadata(?StripeObject $metadata): StoragePaymentMetadata
    {
        if (!$metadata) {
            throw new InternalException('Metadata not found');
        }

        if (!isset($metadata->user_id, $metadata->price_czk, $metadata->size)) {
            throw new InternalException('Invalid metadata');
        }

        $account = $this->accountService->getAccountById($metadata->user_id);

        return new StoragePaymentMetadata(
            $account,
            $metadata->price_czk,
            $metadata->size,
            StoragePaymentType::CARD
        );
    }
}
