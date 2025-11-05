<?php

namespace App\Repositories;

use App\DTOs\ProductDTO;
use App\Interfaces\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * @var array<ProductDTO>
     */
    private array $products;

    public function __construct()
    {
        $this->products = $this->loadProducts();
    }

    /**
     * 
     *
     * @return array<ProductDTO>
     */
    private function loadProducts(): array
    {
        return [
            new ProductDTO(
                id: 1,
                name: 'Laptop Pro 15"',
                description: 'High-performance laptop with 16GB RAM and 512GB SSD',
                price: 3499.99,
                imageUrl: 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=300'
            ),
            new ProductDTO(
                id: 2,
                name: 'Wireless Mouse',
                description: 'Ergonomic wireless mouse with precision tracking',
                price: 89.90,
                imageUrl: 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=300'
            ),
            new ProductDTO(
                id: 3,
                name: 'Mechanical Keyboard',
                description: 'RGB mechanical keyboard with blue switches',
                price: 299.99,
                imageUrl: 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=300'
            ),
            new ProductDTO(
                id: 4,
                name: 'HD Webcam',
                description: '1080p webcam with built-in microphone',
                price: 249.00,
                imageUrl: 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=300'
            ),
            new ProductDTO(
                id: 5,
                name: 'USB-C Hub',
                description: '7-in-1 USB-C hub with HDMI and card reader',
                price: 159.90,
                imageUrl: 'https://images.unsplash.com/photo-1625948515291-69613efd103f?w=300'
            ),
        ];
    }

    /**
     * Get all products
     *
     * @return array<ProductDTO>
     */
    public function getAllProducts(): array
    {
        return $this->products;
    }

    /**
     * 
     *
     * @param int $id
     * @return ProductDTO|null
     */
    public function findProductById(int $id): ?ProductDTO
    {
        foreach ($this->products as $product) {
            if ($product->id === $id) {
                return $product;
            }
        }

        return null;
    }
}

