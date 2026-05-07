<?php

namespace App\Tests\Service;

use App\Service\StripePaymentService;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for StripePaymentService covering amount conversion
 * and publishable key retrieval. Stripe API calls are not tested
 * here (requires integration tests with Stripe mock).
 */
class StripePaymentServiceTest extends TestCase
{
    public function testGetAmountInCentsFromFloat(): void
    {
        $service = new StripePaymentService('sk_test_fake', 'pk_test_fake');

        $this->assertSame(1000, $service->getAmountInCents(10.00));
        $this->assertSame(1050, $service->getAmountInCents(10.50));
        $this->assertSame(9999, $service->getAmountInCents(99.99));
        $this->assertSame(1, $service->getAmountInCents(0.01));
        $this->assertSame(0, $service->getAmountInCents(0.0));
    }

    public function testGetAmountInCentsFromString(): void
    {
        $service = new StripePaymentService('sk_test_fake', 'pk_test_fake');

        $this->assertSame(2500, $service->getAmountInCents('25.00'));
        $this->assertSame(1299, $service->getAmountInCents('12.99'));
    }

    public function testAmountFromCents(): void
    {
        $service = new StripePaymentService('sk_test_fake', 'pk_test_fake');

        $this->assertSame('10.00', $service->amountFromCents(1000));
        $this->assertSame('99.99', $service->amountFromCents(9999));
        $this->assertSame('0.01', $service->amountFromCents(1));
        $this->assertSame('0.00', $service->amountFromCents(0));
    }

    public function testGetPublishableKey(): void
    {
        $service = new StripePaymentService('sk_test_fake', 'pk_test_12345');
        $this->assertSame('pk_test_12345', $service->getPublishableKey());
    }

    public function testGetPublishableKeyTrimsWhitespace(): void
    {
        $service = new StripePaymentService('sk_test_fake', '  pk_test_12345  ');
        $this->assertSame('pk_test_12345', $service->getPublishableKey());
    }

    public function testGetPublishableKeyWhenNull(): void
    {
        $service = new StripePaymentService('sk_test_fake', null);
        $this->assertSame('', $service->getPublishableKey());
    }

    public function testRoundingEdgeCases(): void
    {
        $service = new StripePaymentService('sk_test_fake', 'pk_test_fake');

        // Test rounding: 19.995 should round to 2000 cents
        $this->assertSame(2000, $service->getAmountInCents(19.995));

        // Reverse
        $this->assertSame('19.99', $service->amountFromCents(1999));
    }
}
