<?php

namespace Tests\Feature;

use Tests\TestCase;

class CartApiTest extends TestCase
{
    
    public function test_get_all_products_returns_success(): void
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'price',
                        'image_url'
                    ]
                ]
            ])
            ->assertJson([
                'success' => true
            ]);

        // Verify we have 5 products
        $data = $response->json('data');
        $this->assertCount(5, $data);
    }

   
    public function test_calculate_cart_with_pix_payment(): void
    {
        $response = $this->postJson('/api/cart/calculate', [
            'items' => [
                ['product_id' => 1, 'quantity' => 1]
            ],
            'payment_method' => 'PIX',
            'installments' => 1
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'subtotal',
                    'discount',
                    'interest',
                    'final_value',
                    'payment_method',
                    'installments'
                ]
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'payment_method' => 'PIX',
                    'installments' => 1
                ]
            ]);

        $data = $response->json('data');
        $this->assertGreaterThan(0, $data['discount']);
        $this->assertEquals(0, $data['interest']);
    }

    public function test_calculate_cart_with_credit_card_full_payment(): void
    {
        $response = $this->postJson('/api/cart/calculate', [
            'items' => [
                ['product_id' => 2, 'quantity' => 2]
            ],
            'payment_method' => 'CREDIT_CARD_FULL_PAYMENT',
            'installments' => 1
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'payment_method' => 'CREDIT_CARD_FULL_PAYMENT'
                ]
            ]);

        $data = $response->json('data');
        $this->assertGreaterThan(0, $data['discount']);
        $this->assertEquals(0, $data['interest']);
    }


    public function test_calculate_cart_with_credit_card_installments(): void
    {
        $response = $this->postJson('/api/cart/calculate', [
            'items' => [
                ['product_id' => 1, 'quantity' => 1]
            ],
            'payment_method' => 'CREDIT_CARD_INSTALLMENTS',
            'installments' => 12
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'subtotal',
                    'discount',
                    'interest',
                    'final_value',
                    'payment_method',
                    'installments',
                    'installment_value'
                ]
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'payment_method' => 'CREDIT_CARD_INSTALLMENTS',
                    'installments' => 12
                ]
            ]);

        $data = $response->json('data');
        $this->assertEquals(0, $data['discount']);
        $this->assertGreaterThan(0, $data['interest']);
        $this->assertArrayHasKey('installment_value', $data);
    }


    public function test_calculate_cart_with_empty_items_returns_error(): void
    {
        $response = $this->postJson('/api/cart/calculate', [
            'items' => [],
            'payment_method' => 'PIX',
            'installments' => 1
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false
            ]);
    }

 
    public function test_calculate_cart_with_missing_fields_returns_error(): void
    {
        $response = $this->postJson('/api/cart/calculate', [
            'items' => [
                ['product_id' => 1]
            ],
            'payment_method' => 'PIX'
        ]);

        $response->assertStatus(422);
    }

    public function test_calculate_cart_with_invalid_payment_method_returns_error(): void
    {
        $response = $this->postJson('/api/cart/calculate', [
            'items' => [
                ['product_id' => 1, 'quantity' => 1]
            ],
            'payment_method' => 'INVALID_METHOD',
            'installments' => 1
        ]);

        $response->assertStatus(422);
    }

   
    public function test_calculate_cart_with_invalid_installments_returns_error(): void
    {
        $response = $this->postJson('/api/cart/calculate', [
            'items' => [
                ['product_id' => 1, 'quantity' => 1]
            ],
            'payment_method' => 'CREDIT_CARD_INSTALLMENTS',
            'installments' => 1
        ]);

        $response->assertStatus(400);
    }

  
    public function test_calculate_cart_with_invalid_product_returns_error(): void
    {
        $response = $this->postJson('/api/cart/calculate', [
            'items' => [
                ['product_id' => 999, 'quantity' => 1]
            ],
            'payment_method' => 'PIX',
            'installments' => 1
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false
            ]);
    }

  
    public function test_calculate_cart_with_multiple_items(): void
    {
        $response = $this->postJson('/api/cart/calculate', [
            'items' => [
                ['product_id' => 1, 'quantity' => 2],
                ['product_id' => 2, 'quantity' => 1],
                ['product_id' => 3, 'quantity' => 3]
            ],
            'payment_method' => 'PIX',
            'installments' => 1
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $data = $response->json('data');
        $this->assertGreaterThan(0, $data['subtotal']);
        $this->assertGreaterThan(0, $data['discount']);
        $this->assertLessThan($data['subtotal'], $data['final_value']);
    }
}

