<?php

namespace App\Models\Concerns;

use App\Models\Image;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Imageable
{
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}