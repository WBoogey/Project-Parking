<?php

namespace App\Infrastructure\adaptaters;

use App\Domain\Port\PaymentGatewayInterface;
use App\Domain\Payment\PaymentRequest;
use App\Domain\Payment\PaymentResult;
use App\Domain\Payment\PaymentStatus;
use App\Domain\Payment\RefundResult;
use App\Domain\Payment\CustomerData;
use App\Domain\Payment\PaymentException;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Refund;
use Stripe\Exception\ApiErrorException;

class StripePaymentAdapter implements PaymentGatewayInterface
{
    public function __construct(
        private readonly string $secretKey,
        private readonly string $defaultSuccessUrl = 'http://localhost:3000/payment/success',
        private readonly string $defaultCancelUrl = 'http://localhost:3000/payment/cancel',
    ) {
        Stripe::setApiKey($this->secretKey);
    }

    public function createPayment(PaymentRequest $request): PaymentResult
    {
        try {
            $sessionParams = [
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => $request->currency,
                        'product_data' => [
                            'name' => $request->description,
                        ],
                        'unit_amount' => $request->amount,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $request->successUrl ?? $this->defaultSuccessUrl,
                'cancel_url' => $request->cancelUrl ?? $this->defaultCancelUrl,
            ];

            if ($request->customerId !== null) {
                $sessionParams['customer'] = $request->customerId;
            }

            if (!empty($request->metadata)) {
                $sessionParams['metadata'] = $request->metadata;
            }

            $session = Session::create($sessionParams);

            return new PaymentResult(
                paymentId: $session->id,
                status: PaymentStatus::PENDING,
                checkoutUrl: $session->url,
            );
        } catch (ApiErrorException $e) {
            throw new PaymentException($e->getMessage(), $e->getCode());
        }
    }

    public function refund(string $paymentId, int $amount): RefundResult
    {
        try {
            $session = Session::retrieve($paymentId);

            if ($session->payment_intent === null) {
                return new RefundResult(
                    refundId: '',
                    success: false,
                    errorMessage: 'No payment intent found for this session',
                );
            }

            $refund = Refund::create([
                'payment_intent' => $session->payment_intent,
                'amount' => $amount,
            ]);

            return new RefundResult(
                refundId: $refund->id,
                success: true,
            );
        } catch (ApiErrorException $e) {
            return new RefundResult(
                refundId: '',
                success: false,
                errorMessage: $e->getMessage(),
            );
        }
    }

    public function getPaymentStatus(string $paymentId): PaymentStatus
    {
        try {
            $session = Session::retrieve($paymentId);

            return match ($session->payment_status) {
                'paid' => PaymentStatus::SUCCESS,
                'unpaid' => PaymentStatus::PENDING,
                'no_payment_required' => PaymentStatus::SUCCESS,
                default => PaymentStatus::FAILED,
            };
        } catch (ApiErrorException) {
            return PaymentStatus::FAILED;
        }
    }

    public function createCustomer(CustomerData $customer): string
    {
        try {
            $params = [
                'email' => $customer->email,
                'name' => $customer->name,
            ];

            if ($customer->phone !== null) {
                $params['phone'] = $customer->phone;
            }

            if ($customer->userId !== null) {
                $params['metadata'] = ['user_id' => $customer->userId];
            }

            $stripeCustomer = Customer::create($params);

            return $stripeCustomer->id;
        } catch (ApiErrorException $e) {
            throw new PaymentException($e->getMessage(), $e->getCode());
        }
    }

    public function getCustomer(string $customerId): ?CustomerData
    {
        try {
            $stripeCustomer = Customer::retrieve($customerId);

            if ($stripeCustomer->isDeleted()) {
                return null;
            }

            return new CustomerData(
                email: $stripeCustomer->email ?? '',
                name: $stripeCustomer->name ?? '',
                phone: $stripeCustomer->phone,
                userId: $stripeCustomer->metadata['user_id'] ?? null,
            );
        } catch (ApiErrorException) {
            return null;
        }
    }

    public function cancelPayment(string $paymentId): bool
    {
        try {
            $session = Session::retrieve($paymentId);

            if ($session->payment_status === 'unpaid') {
                $session->expire();
                return true;
            }

            return false;
        } catch (ApiErrorException) {
            return false;
        }
    }
}
