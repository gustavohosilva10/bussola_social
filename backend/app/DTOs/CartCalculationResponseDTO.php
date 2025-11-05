<?php

namespace App\DTOs;

class CartCalculationResponseDTO
{
    public function __construct(
        public readonly float $subtotal,
        public readonly float $discount,
        public readonly float $interest,
        public readonly float $finalValue,
        public readonly string $paymentMethod,
        public readonly int $installments,
        public readonly ?float $installmentValue = null
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'subtotal' => round($this->subtotal, 2),
            'discount' => round($this->discount, 2),
            'interest' => round($this->interest, 2),
            'final_value' => round($this->finalValue, 2),
            'payment_method' => $this->paymentMethod,
            'installments' => $this->installments,
        ];

        if ($this->installmentValue !== null) {
            $data['installment_value'] = round($this->installmentValue, 2);
        }

        return $data;
    }
}

