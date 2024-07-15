<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Repositories\CategoryRepository;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{    
    /**
     * __invoke
     *
     * @param  CategoryRepository $categoryRepository
     * @return JsonResponse
     */
    public function __invoke(CategoryRepository $categoryRepository): JsonResponse
    {
        return response()->json(CategoryResource::collection($categoryRepository->getAll()));
    }
}
