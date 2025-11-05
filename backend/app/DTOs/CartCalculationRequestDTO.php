<?php

namespace App\DTOs;

class CartCalculationRequestDTO
{
    
    public function __construct(
        public readonly array $items,
        public readonly string $paymentMethod,
        public readonly int $installments = 1
    ) {
    }

    public static function fromArray(array $data): self
    {
        $items = array_map(
            fn(array $item) => CartItemDTO::fromArray($item),
            $data['items'] ?? []
        );

        return new self(
            items: $items,
            paymentMethod: $data['payment_method'] ?? '',
            installments: (int) ($data['installments'] ?? 1)
        );
    }
}

