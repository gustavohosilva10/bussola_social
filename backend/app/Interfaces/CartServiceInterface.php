<?php

namespace App\Interfaces;

use App\DTOs\CartCalculationRequestDTO;
use App\DTOs\CartCalculationResponseDTO;

interface CartServiceInterface
{
    public function calculateCart(CartCalculationRequestDTO $request): CartCalculationResponseDTO;
}

