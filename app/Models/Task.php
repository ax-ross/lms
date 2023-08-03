<?php

namespace App\Models;

use App\Models\Concerns\Imageable;
use App\Models\Contracts\HasImages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model implements HasImages
{
    use HasFactory, Imageable;

    protected $fillable = ['title', 'lesson_id', 'description', 'is_required'];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
