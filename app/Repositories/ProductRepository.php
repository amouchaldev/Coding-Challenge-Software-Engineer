<?php

namespace App\Repositories;

use App\Models\Product;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * getAll
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        try {
            return Product::get();
        } catch (Throwable) {
            throw new Throwable("Error fetching all products");
        }
    }

    public function getAllSortedAndFiltered(?string $sortDirection = null, ?int $categoryId = null): Collection
    {
        try {
            $query = Product::query();

            if ($categoryId) {
                $query->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('categories.id', $categoryId);
                });
            }

            if ($sortDirection) {
                $sortDirection = strtoupper($sortDirection);
                if (!in_array($sortDirection, ['ASC', 'DESC'])) {
                    $sortDirection = 'ASC';
                }
                $query->orderBy('price', $sortDirection);
            }

            return $query->get();
        } catch (Throwable) {
            throw new Exception('Error fetching sorted and filtered products');
        }
    }

    public function create(array $attributes): Product
    {
        return DB::transaction(function () use ($attributes) {
            $product = Product::create($attributes);

            if (!$product) {
                throw new Exception('Failed to create product');
            }

            $categoryIds = data_get($attributes, 'categories');

            if ($categoryIds) {
                $product->categories()->attach($categoryIds);
            }

            return $product;
        });
    }
}
