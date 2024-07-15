<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function getAll(): Collection;
    public function getAllSortedAndFiltered(?string $sortDirection = 'asc', ?int $categoryId = null): Collection;
    public function create(array $attributes): Product;
}
