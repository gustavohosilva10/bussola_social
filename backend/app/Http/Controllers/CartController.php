<?php

namespace App\Http\Controllers;

use App\DTOs\CartCalculationRequestDTO;
use App\Http\Requests\CartCalculationRequest;
use App\Interfaces\CartServiceInterface;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class CartController extends Controller
{
    public function __construct(
        private readonly CartServiceInterface $cartService
    ) {
    }

    /**
     * 
     *
     * @param CartCalculationRequest $request
     * @return JsonResponse
     */
    public function calculate(CartCalculationRequest $request): JsonResponse
    {
        try {
            $dto = CartCalculationRequestDTO::fromArray($request->validated());
            $result = $this->cartService->calculateCart($dto);

            return response()->json([
                'success' => true,
                'data' => $result->toArray()
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while calculating the cart'
            ], 500);
        }
    }
}

