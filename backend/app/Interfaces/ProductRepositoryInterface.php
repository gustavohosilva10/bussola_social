<?php

namespace App\Interfaces;

use App\DTOs\ProductDTO;

interface ProductRepositoryInterface
{
    public function getAllProducts(): array;
    public function findProductById(int $id): ?ProductDTO;
}

