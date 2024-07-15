<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Repositories\ProductRepository;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class ProductController extends Controller
{
    private $productRepository;

    private $productService;

    /**
     * Create a new controller instance.
     *
     * @param  ProductRepository  $productRepository
     * @param  ProductService  $productService
     * @return void
     */
    public function __construct(ProductRepository $productRepository, ProductService $productService)
    {
        $this->productRepository = $productRepository;
        $this->productService = $productService;
    }

    /**
     * index
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $sortDirection = $request->query('sort');
            $categoryId = $request->query('category');

            $products = $sortDirection || $categoryId
                ? $this->productRepository->getAllSortedAndFiltered($sortDirection, $categoryId)
                : $this->productRepository->getAll();

            $products->load('categories:id,name');

            return response()->json(ProductResource::collection($products));
        } catch (ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Products not found',
            ], Response::HTTP_NOT_FOUND);
        } catch (Throwable) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching products',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * store
     *
     * @param  ProductRequest $request
     * @return JsonResponse
     */
    public function store(ProductRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $product = $this->productService->createProduct($validated);

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'product' => new ProductResource($product),
            ], Response::HTTP_CREATED);
        } catch (Throwable) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
