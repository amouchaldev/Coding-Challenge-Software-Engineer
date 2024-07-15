<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Services\ImageService;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function createProduct(array $validatedData)
    {
        $validatedData['image'] = ImageService::storeImage($validatedData['image']);

        if (isset($validatedData['categories']) && !is_array($validatedData['categories'])) {
            $validatedData['categories'] = explode(',', $validatedData['categories']);
        }

        return $this->productRepository->create($validatedData);
    }
}
