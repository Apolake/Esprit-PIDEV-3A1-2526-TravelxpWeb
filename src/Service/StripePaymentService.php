<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\PaymentIntent;
use Stripe\StripeClient;

class StripePaymentService
{
    private readonly StripeClient $stripeClient;

    public function __construct(
        #[Autowire('%env(string:STRIPE_SECRET_KEY)%')]
        private readonly string $secretKey,
        #[Autowire('%env(default::STRIPE_PUBLISHABLE_KEY)%')]
        private readonly ?string $publishableKey,
    ) {
        $this->stripeClient = new StripeClient($this->secretKey); // Initialize Stripe client with the secret key
    }

    public function getPublishableKey(): string
    {
        return trim((string) $this->publishableKey);
    }

    public function createWalletTopUpIntent(float $amount, User $user, string $currency = 'usd'): PaymentIntent
    {
        if (!str_starts_with($this->secretKey, 'sk_')) {
            throw new \RuntimeException('Stripe secret key is invalid. Configure STRIPE_SECRET_KEY with an sk_test or sk_live key.');
        }

        $amountInCents = $this->getAmountInCents($amount);
        if ($amountInCents <= 0) {
            throw new \RuntimeException('Top-up amount must be greater than 0.');
        }

        return $this->stripeClient->paymentIntents->create([
            'amount' => $amountInCents,
            'currency' => strtolower(trim($currency)), // Ensure currency code is lowercase and trimmed
            'automatic_payment_methods' => ['enabled' => true],
            'metadata' => [
                'type' => 'wallet_top_up',
                'user_id' => (string) $user->getId(),
            ],
        ]);
    }

    public function createWalletTopUpCheckoutSession(
        float $amount,
        User $user,
        int $paymentId,
        string $successUrl,
        string $cancelUrl,
        string $currency = 'usd',
    ): CheckoutSession {
        if (!str_starts_with($this->secretKey, 'sk_')) {
            throw new \RuntimeException('Stripe secret key is invalid. Configure STRIPE_SECRET_KEY with an sk_test or sk_live key.');
        }

        $amountInCents = $this->getAmountInCents($amount);
        if ($amountInCents <= 0) {
            throw new \RuntimeException('Top-up amount must be greater than 0.');
        }

        return $this->stripeClient->checkout->sessions->create([
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'payment_method_types' => ['card'],
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => strtolower(trim($currency)),
                    'unit_amount' => $amountInCents,
                    'product_data' => [
                        'name' => 'TravelXP Wallet Recharge',
                        'description' => sprintf('Wallet top-up for user #%d', (int) $user->getId()),
                    ],
                ],
            ]],
            'metadata' => [
                'type' => 'wallet_top_up',
                'user_id' => (string) $user->getId(),
                'payment_id' => (string) $paymentId,
            ],
        ]);
    }

    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntent
    {
        return $this->stripeClient->paymentIntents->retrieve($paymentIntentId, []);
    }

    public function retrieveCheckoutSession(string $sessionId): CheckoutSession
    {
        return $this->stripeClient->checkout->sessions->retrieve($sessionId, [
            'expand' => ['payment_intent'],
        ]);
    }

    public function getAmountInCents(float|string $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    public function amountFromCents(int $amountInCents): string
    {
        return number_format($amountInCents / 100, 2, '.', '');
    }
}
