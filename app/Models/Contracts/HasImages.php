<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface HasImages
{
    public function images(): MorphMany;
}