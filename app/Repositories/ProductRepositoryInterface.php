<?php

namespace App\Repositories;

interface ProductRepositoryInterface
{
    public function getAll();
    public function getAllSortedAndFiltered(?string $sortDirection = 'asc', ?int $categoryId = null);
    public function create(array $attributes);
}
