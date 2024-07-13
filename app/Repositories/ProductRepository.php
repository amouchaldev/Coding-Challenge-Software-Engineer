<?php

namespace App\Repositories;

use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAll()
    {
        try {
            return Product::with('categories:id,name')->get();
        } catch (Exception $e) {
            throw new Exception("Error fetching all products: " . $e->getMessage());
        }
    }

    public function getAllSortedAndFiltered(?string $sortDirection = null, ?int $categoryId = null)
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
                if(!in_array($sortDirection, ['ASC', 'DESC'])) {
                    $sortDirection = 'ASC';
                }
                // dd($query->orderBy('price', $sortDirection));
                $query->orderBy('price', $sortDirection);
            }

            return $query->get();
        } catch (Exception $e) {
            throw new Exception("Error fetching sorted and filtered products: " . $e->getMessage());
        }
    }

    public function create(array $attributes)
    {
        return DB::transaction(function () use ($attributes) {
            $product = Product::make($attributes);
            if (isset($attributes['image']) && file_exists($attributes['image'])) {
                $product->image = Storage::disk('public')->put('products', $attributes['image']);
            } else {
                throw new \Exception('Image file not found or invalid.');
            }

            $product->save();

            if (!$product) {
                throw new \Exception('Failed to create product');
            }

            $categoryIds = data_get($attributes, 'categories');

            if ($categoryIds) {
                $product->categories()->attach($categoryIds);
            }

            return $product;
        });
    }
}
