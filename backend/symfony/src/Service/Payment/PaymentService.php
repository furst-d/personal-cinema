<?php

namespace App\Service\Payment;

use App\Entity\Account\Account;
use App\Entity\Storage\StorageUpgradePrice;
use App\Exception\InternalException;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentService
{
    /**
     * @var string $frontendUrl
     */
    private string $frontendUrl;

    /**
     * @param string $stripeSecretKey
     * @param string $frontendUrl
     */
    public function __construct(string $stripeSecretKey, string $frontendUrl)
    {
        Stripe::setApiKey($stripeSecretKey);
        $this->frontendUrl = $frontendUrl;
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
                'success_url' => "$this->frontendUrl/payment/success",
                'cancel_url' => "$this->frontendUrl/payment/cancel",
                'metadata' => [
                    'user_id' => $account->getId(),
                    'price_id' => $price->getId(),
                    'price_czk' => $price->getDiscountedPriceCzk(),
                ],
            ]);
        } catch (ApiErrorException) {
            throw new InternalException('Failed to create checkout session');
        }
    }

    /**
     * @param string $paymentIntentId
     * @return PaymentIntent
     * @throws InternalException
     */
    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntent
    {
        try {
            return PaymentIntent::retrieve($paymentIntentId);
        } catch (ApiErrorException $e) {
            throw new InternalException('Failed to retrieve payment intent');
        }
    }
}
