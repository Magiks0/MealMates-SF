<?php

namespace App\Service;

use App\Entity\Product;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Symfony\Bundle\SecurityBundle\Security;

class StripeService
{
    private $stripeClient;

    public function __construct(private Security $security){}

    private function getStripeClient(): StripeClient
    {
        return $this->stripeClient ??= new StripeClient($_ENV['STRIPE_API_SECRET']);
    }

    public function createProduct(Product $product)
    {
        return $this->getStripeClient()->products->create([
            'name' => $product->getTitle(),
            'description' => $product->getDescription(),
            'active' => $product->isPublished(),
        ]);
    }

    public function createPrice(Product $product)
    {
        return $this->getStripeClient()->prices->create([
            'unit_amount' => $product->getPrice()*100,
            'currency' => 'EUR',
            'product' => $product->getStripeProductId(),
        ]);
    }

    public function updateProduct(Product $product): \Stripe\Product
    {
        return $this->getStripeClient()->products->update(
            $product->getStripeProductId(),
            [
                'name' => $product->getTitle(),
                'description' => $product->getDescription(),
            ]
        );
    }

    public function updatePrice(Product $product): \Stripe\Price
    {
        return $this->getStripeClient()->prices->update(
            $product->getStripePriceId(),
            [
                'unit_amount' => $product->getPrice()*100,
                'currency' => 'EUR',
                'product' => $product->getStripeProductId(),
            ]
        );
    }


    /**
     * @throws ApiErrorException
     */
    public function createCheckoutSession(Product $product): Session
    {
        return $this->getStripeClient()->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => [[
                'price' => $product->getStripePriceId(),
                'quantity' => 1,
            ]],
            'metadata' => [
                'product_id' => $product->getId(),
                'user_id' => $this->security->getUser()->getId(),
            ],
            'success_url' => $_ENV['DOMAIN'].'/stripe/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $_ENV['FRONT_DOMAIN'].'/product/'.$product->getId(),
        ]);
    }

}