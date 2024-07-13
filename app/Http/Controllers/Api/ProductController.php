<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Repositories\ProductRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{    
    /**
     * index
     *
     * @param  mixed $request
     * @param  mixed $model
     * @return JsonResponse
     */
    public function index(Request $request, ProductRepository $model): JsonResponse
    {
        try {
            $sortDirection = $request->query('sort');
            $categoryId = $request->query('category');

            if ($sortDirection || $categoryId) {
                $products = $model->getAllSortedAndFiltered($sortDirection, $categoryId);
            } else {
                $products = $model->getAll();
            }
            return response()->json(
                ProductResource::collection($products)
            );

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching products: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * store
     *
     * @param  mixed $request
     * @param  mixed $model
     * @return JsonResponse
     */
    public function store(ProductRequest $request, ProductRepository $model): JsonResponse
    {
        try {
            $validated = $request->validated();

            $product = $model->create($validated);

            return response()->json([
                'success'  => true,
                'message' => 'Product created successfully',
                'product' => new ProductResource($product),
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product: '. $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
