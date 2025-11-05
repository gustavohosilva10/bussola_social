<?php

namespace App\Http\Controllers;

use App\Interfaces\ProductRepositoryInterface;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }

    /**
     * 
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $products = $this->productRepository->getAllProducts();

        $productsArray = array_map(
            fn($product) => $product->toArray(),
            $products
        );

        return response()->json([
            'success' => true,
            'data' => $productsArray
        ]);
    }
}

