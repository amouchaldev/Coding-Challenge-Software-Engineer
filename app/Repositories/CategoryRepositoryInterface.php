<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{    
    /**
     * getAll
     *
     * @return Collection
     */
    public function getAll(): Collection;
}
