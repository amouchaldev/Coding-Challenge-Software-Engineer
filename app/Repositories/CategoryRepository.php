<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAll()
    {
        return Cache::remember('categories', now()->addWeek(), fn() => Category::get());
    }
}
