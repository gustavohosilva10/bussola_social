<?php

namespace App\Services;

use App\DTOs\CartCalculationRequestDTO;
use App\DTOs\CartCalculationResponseDTO;
use App\Interfaces\CartServiceInterface;
use App\Interfaces\ProductRepositoryInterface;
use InvalidArgumentException;

class CartService implements CartServiceInterface
{
    private const PAYMENT_METHOD_PIX = 'PIX';
    private const PAYMENT_METHOD_CREDIT_CARD_FULL = 'CREDIT_CARD_FULL_PAYMENT';
    private const PAYMENT_METHOD_CREDIT_CARD_INSTALLMENTS = 'CREDIT_CARD_INSTALLMENTS';

    private const DISCOUNT_RATE = 0.10; 
    private const INTEREST_RATE = 0.01; 
    private const MIN_INSTALLMENTS = 2;
    private const MAX_INSTALLMENTS = 12;

    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }

    public function calculateCart(CartCalculationRequestDTO $request): CartCalculationResponseDTO
    {
        $this->validateRequest($request);

        $subtotal = $this->calculateSubtotal($request);
        $discount = 0.0;
        $interest = 0.0;
        $installmentValue = null;

        $finalValue = match ($request->paymentMethod) {
            self::PAYMENT_METHOD_PIX => $this->applyDiscount($subtotal, $discount),
            self::PAYMENT_METHOD_CREDIT_CARD_FULL => $this->applyDiscount($subtotal, $discount),
            self::PAYMENT_METHOD_CREDIT_CARD_INSTALLMENTS => $this->applyCompoundInterest(
                $subtotal,
                $request->installments,
                $interest,
                $installmentValue
            ),
            default => throw new InvalidArgumentException("Invalid payment method: {$request->paymentMethod}")
        };

        return new CartCalculationResponseDTO(
            subtotal: $subtotal,
            discount: $discount,
            interest: $interest,
            finalValue: $finalValue,
            paymentMethod: $request->paymentMethod,
            installments: $request->installments,
            installmentValue: $installmentValue
        );
    }

    /**
     * 
     *
     * @param CartCalculationRequestDTO $request
     * @throws InvalidArgumentException
     */
    private function validateRequest(CartCalculationRequestDTO $request): void
    {
        if (empty($request->items)) {
            throw new InvalidArgumentException('Cart items cannot be empty');
        }

        if ($request->paymentMethod === self::PAYMENT_METHOD_CREDIT_CARD_INSTALLMENTS) {
            if ($request->installments < self::MIN_INSTALLMENTS || $request->installments > self::MAX_INSTALLMENTS) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Installments must be between %d and %d',
                        self::MIN_INSTALLMENTS,
                        self::MAX_INSTALLMENTS
                    )
                );
            }
        }
    }

    /**
     * 
     *
     * @param CartCalculationRequestDTO $request
     * @return float
     */
    private function calculateSubtotal(CartCalculationRequestDTO $request): float
    {
        $subtotal = 0.0;

        foreach ($request->items as $item) {
            $product = $this->productRepository->findProductById($item->productId);

            if ($product === null) {
                throw new InvalidArgumentException("Product with ID {$item->productId} not found");
            }

            $subtotal += $product->price * $item->quantity;
        }

        return $subtotal;
    }

   
    private function applyDiscount(float $subtotal, float &$discount): float
    {
        $discount = $subtotal * self::DISCOUNT_RATE;
        return $subtotal - $discount;
    }

 
    private function applyCompoundInterest(
        float $subtotal,
        int $installments,
        float &$interest,
        ?float &$installmentValue
    ): float {
        $finalValue = $subtotal * pow(1 + self::INTEREST_RATE, $installments);
        $interest = $finalValue - $subtotal;
        $installmentValue = $finalValue / $installments;

        return $finalValue;
    }
}

