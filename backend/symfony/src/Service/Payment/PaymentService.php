<?php

namespace App\Service\Payment;

use App\Exception\InternalException;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentService
{
    /**
     * @param string $stripeSecretKey
     */
    public function __construct(string $stripeSecretKey)
    {
        Stripe::setApiKey($stripeSecretKey);
    }

    /**
     * @param int $amount
     * @return PaymentIntent
     * @throws InternalException
     */
    public function createPaymentIntent(int $amount): PaymentIntent
    {
        try {
            return PaymentIntent::create([
                'amount' => $amount * 100, // amount in cents
                'currency' => 'czk',
                'payment_method_types' => ['card'],
            ]);
        } catch (ApiErrorException $e) {
            throw new InternalException('Failed to create payment intent: ' . $e->getMessage());
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
