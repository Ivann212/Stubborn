<?php

namespace App\Service;

use Stripe\StripeClient;
use Stripe\Checkout\Session;

class StripeService
{
    private StripeClient $stripe;
    private string $publicKey;

    public function __construct(string $stripe_public_key, string $stripe_secret_key)
    {
        $this->publicKey = $stripe_public_key;
        $this->stripe = new StripeClient($stripe_secret_key);
    }

    public function createCheckoutSession(array $lineItems, string $successUrl, string $cancelUrl): Session
    {
        return $this->stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems, // Correction ici
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }
}
