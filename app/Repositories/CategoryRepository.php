<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class CategoryRepository implements CategoryRepositoryInterface
{    
    /**
     * getAll
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return Cache::remember('categories', now()->addWeek(), fn() => Category::get());
    }
}
