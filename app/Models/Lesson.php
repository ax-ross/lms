<?php

namespace App\Models;

use App\Models\Concerns\Imageable;
use App\Models\Contracts\HasImages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model implements HasImages
{
    use HasFactory, Imageable;

    protected $fillable = ['title', 'content', 'section_id'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
