<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class ProductController extends Controller
{
    /**
     * index
     *
     * @param  Request $request
     * @param  ProductRepository $model
     * @return JsonResponse
     */
    public function index(Request $request, ProductRepository $model): JsonResponse
    {
        try {
            $sortDirection = $request->query('sort');
            $categoryId = $request->query('category');

            $products = $sortDirection || $categoryId 
                ? $model->getAllSortedAndFiltered($sortDirection, $categoryId)
                : $model->getAll();

            return response()->json(ProductResource::collection($products));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Products not found: ' . $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
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
     * @param  ProductRequest $request
     * @param  ProductRepository $model
     * @return JsonResponse
     */
    public function store(ProductRequest $request, ProductRepository $model): JsonResponse
    {
        try {
            $validated = $request->validated();

            if (isset($validated['categories']) && !is_array($validated['categories'])) {
                $validated['categories'] = explode(',', $validated['categories']);
            }

            $product = $model->create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'product' => new ProductResource($product),
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product: ' . $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
