<?php

namespace Tests\Unit;

use App\DTOs\CartCalculationRequestDTO;
use App\DTOs\CartItemDTO;
use App\Interfaces\ProductRepositoryInterface;
use App\Services\CartService;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class CartServiceTest extends TestCase
{
    private CartService $cartService;
    private ProductRepositoryInterface $productRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the product repository
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->cartService = new CartService($this->productRepository);
    }

    /**
     * Test PIX payment method applies 10% discount
     */
    public function test_pix_payment_applies_ten_percent_discount(): void
    {
        // Mock product
        $product = new \App\DTOs\ProductDTO(
            id: 1,
            name: 'Test Product',
            description: 'Test Description',
            price: 100.00,
            imageUrl: 'test.jpg'
        );

        $this->productRepository
            ->method('findProductById')
            ->willReturn($product);

        // Create request with PIX payment
        $request = new CartCalculationRequestDTO(
            items: [new CartItemDTO(productId: 1, quantity: 1)],
            paymentMethod: 'PIX',
            installments: 1
        );

        $result = $this->cartService->calculateCart($request);

        $this->assertEquals(100.00, $result->subtotal);
        $this->assertEquals(10.00, $result->discount); // 10% discount
        $this->assertEquals(0.00, $result->interest);
        $this->assertEquals(90.00, $result->finalValue); // 100 - 10
    }

    /**
     * Test Credit Card Full Payment applies 10% discount
     */
    public function test_credit_card_full_payment_applies_ten_percent_discount(): void
    {
        $product = new \App\DTOs\ProductDTO(
            id: 1,
            name: 'Test Product',
            description: 'Test Description',
            price: 200.00,
            imageUrl: 'test.jpg'
        );

        $this->productRepository
            ->method('findProductById')
            ->willReturn($product);

        $request = new CartCalculationRequestDTO(
            items: [new CartItemDTO(productId: 1, quantity: 1)],
            paymentMethod: 'CREDIT_CARD_FULL_PAYMENT',
            installments: 1
        );

        $result = $this->cartService->calculateCart($request);

        $this->assertEquals(200.00, $result->subtotal);
        $this->assertEquals(20.00, $result->discount); // 10% discount
        $this->assertEquals(0.00, $result->interest);
        $this->assertEquals(180.00, $result->finalValue); // 200 - 20
    }

    /**
     * Test Credit Card Installments applies compound interest
     * Formula: M = P × (1 + i)^n
     */
    public function test_credit_card_installments_applies_compound_interest(): void
    {
        $product = new \App\DTOs\ProductDTO(
            id: 1,
            name: 'Test Product',
            description: 'Test Description',
            price: 1000.00,
            imageUrl: 'test.jpg'
        );

        $this->productRepository
            ->method('findProductById')
            ->willReturn($product);

        // Test with 12 installments
        $request = new CartCalculationRequestDTO(
            items: [new CartItemDTO(productId: 1, quantity: 1)],
            paymentMethod: 'CREDIT_CARD_INSTALLMENTS',
            installments: 12
        );

        $result = $this->cartService->calculateCart($request);

        // M = 1000 × (1.01)^12 = 1126.825030131969720407
        $expectedFinalValue = 1000 * pow(1.01, 12);
        $expectedInterest = $expectedFinalValue - 1000;
        $expectedInstallmentValue = $expectedFinalValue / 12;

        $this->assertEquals(1000.00, $result->subtotal);
        $this->assertEquals(0.00, $result->discount);
        $this->assertEqualsWithDelta($expectedInterest, $result->interest, 0.01);
        $this->assertEqualsWithDelta($expectedFinalValue, $result->finalValue, 0.01);
        $this->assertEqualsWithDelta($expectedInstallmentValue, $result->installmentValue, 0.01);
        $this->assertEquals(12, $result->installments);
    }

    /**
     * Test compound interest with 2 installments
     */
    public function test_compound_interest_with_two_installments(): void
    {
        $product = new \App\DTOs\ProductDTO(
            id: 1,
            name: 'Test Product',
            description: 'Test Description',
            price: 500.00,
            imageUrl: 'test.jpg'
        );

        $this->productRepository
            ->method('findProductById')
            ->willReturn($product);

        $request = new CartCalculationRequestDTO(
            items: [new CartItemDTO(productId: 1, quantity: 1)],
            paymentMethod: 'CREDIT_CARD_INSTALLMENTS',
            installments: 2
        );

        $result = $this->cartService->calculateCart($request);

        // M = 500 × (1.01)^2 = 510.05
        $expectedFinalValue = 500 * pow(1.01, 2);
        
        $this->assertEquals(500.00, $result->subtotal);
        $this->assertEqualsWithDelta($expectedFinalValue, $result->finalValue, 0.01);
        $this->assertEqualsWithDelta(10.05, $result->interest, 0.01);
    }

    /**
     * Test multiple items in cart
     */
    public function test_multiple_items_in_cart(): void
    {
        $product1 = new \App\DTOs\ProductDTO(
            id: 1,
            name: 'Product 1',
            description: 'Description 1',
            price: 100.00,
            imageUrl: 'test1.jpg'
        );

        $product2 = new \App\DTOs\ProductDTO(
            id: 2,
            name: 'Product 2',
            description: 'Description 2',
            price: 200.00,
            imageUrl: 'test2.jpg'
        );

        $this->productRepository
            ->method('findProductById')
            ->willReturnCallback(function($id) use ($product1, $product2) {
                return $id === 1 ? $product1 : $product2;
            });

        $request = new CartCalculationRequestDTO(
            items: [
                new CartItemDTO(productId: 1, quantity: 2), // 200
                new CartItemDTO(productId: 2, quantity: 1), // 200
            ],
            paymentMethod: 'PIX',
            installments: 1
        );

        $result = $this->cartService->calculateCart($request);

        $this->assertEquals(400.00, $result->subtotal); // 200 + 200
        $this->assertEquals(40.00, $result->discount); // 10%
        $this->assertEquals(360.00, $result->finalValue); // 400 - 40
    }

    /**
     * Test validation: empty cart
     */
    public function test_empty_cart_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cart items cannot be empty');

        $request = new CartCalculationRequestDTO(
            items: [],
            paymentMethod: 'PIX',
            installments: 1
        );

        $this->cartService->calculateCart($request);
    }

    /**
     * Test validation: invalid installments (less than 2)
     */
    public function test_invalid_installments_less_than_minimum_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Installments must be between 2 and 12');

        $product = new \App\DTOs\ProductDTO(
            id: 1,
            name: 'Test Product',
            description: 'Test Description',
            price: 100.00,
            imageUrl: 'test.jpg'
        );

        $this->productRepository
            ->method('findProductById')
            ->willReturn($product);

        $request = new CartCalculationRequestDTO(
            items: [new CartItemDTO(productId: 1, quantity: 1)],
            paymentMethod: 'CREDIT_CARD_INSTALLMENTS',
            installments: 1
        );

        $this->cartService->calculateCart($request);
    }

    /**
     * Test validation: invalid installments (more than 12)
     */
    public function test_invalid_installments_more_than_maximum_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Installments must be between 2 and 12');

        $product = new \App\DTOs\ProductDTO(
            id: 1,
            name: 'Test Product',
            description: 'Test Description',
            price: 100.00,
            imageUrl: 'test.jpg'
        );

        $this->productRepository
            ->method('findProductById')
            ->willReturn($product);

        $request = new CartCalculationRequestDTO(
            items: [new CartItemDTO(productId: 1, quantity: 1)],
            paymentMethod: 'CREDIT_CARD_INSTALLMENTS',
            installments: 13
        );

        $this->cartService->calculateCart($request);
    }

    /**
     * Test validation: product not found
     */
    public function test_product_not_found_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Product with ID 999 not found');

        $this->productRepository
            ->method('findProductById')
            ->willReturn(null);

        $request = new CartCalculationRequestDTO(
            items: [new CartItemDTO(productId: 999, quantity: 1)],
            paymentMethod: 'PIX',
            installments: 1
        );

        $this->cartService->calculateCart($request);
    }

    /**
     * Test validation: invalid payment method
     */
    public function test_invalid_payment_method_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid payment method: INVALID_METHOD');

        $product = new \App\DTOs\ProductDTO(
            id: 1,
            name: 'Test Product',
            description: 'Test Description',
            price: 100.00,
            imageUrl: 'test.jpg'
        );

        $this->productRepository
            ->method('findProductById')
            ->willReturn($product);

        $request = new CartCalculationRequestDTO(
            items: [new CartItemDTO(productId: 1, quantity: 1)],
            paymentMethod: 'INVALID_METHOD',
            installments: 1
        );

        $this->cartService->calculateCart($request);
    }

    /**
     * Test compound interest formula precision with different installment counts
     */
    public function test_compound_interest_formula_precision(): void
    {
        $product = new \App\DTOs\ProductDTO(
            id: 1,
            name: 'Test Product',
            description: 'Test Description',
            price: 1000.00,
            imageUrl: 'test.jpg'
        );

        $this->productRepository
            ->method('findProductById')
            ->willReturn($product);

        $testCases = [
            ['installments' => 2, 'expected' => 1000 * pow(1.01, 2)],
            ['installments' => 6, 'expected' => 1000 * pow(1.01, 6)],
            ['installments' => 12, 'expected' => 1000 * pow(1.01, 12)],
        ];

        foreach ($testCases as $testCase) {
            $request = new CartCalculationRequestDTO(
                items: [new CartItemDTO(productId: 1, quantity: 1)],
                paymentMethod: 'CREDIT_CARD_INSTALLMENTS',
                installments: $testCase['installments']
            );

            $result = $this->cartService->calculateCart($request);

            $this->assertEqualsWithDelta(
                $testCase['expected'],
                $result->finalValue,
                0.01,
                "Failed for {$testCase['installments']} installments"
            );
        }
    }
}

