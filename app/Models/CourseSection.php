<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSection extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'section_number', 'course_id'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
