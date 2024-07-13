<?php

namespace App\Repositories;
namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function getAll();
    public function getAllSortedAndFiltered(?string $sortDirection = 'asc', ?int $categoryId = null);
    public function create(array $attributes);
}
