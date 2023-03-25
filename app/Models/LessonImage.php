<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonImage extends Model
{
    use HasFactory;

    protected $fillable = ['path', 'lesson_id'];
    
    public static function findImageByAbsolutePath(string $absolutePath)
    {
        $path = parse_url($absolutePath, PHP_URL_PATH);
        if (str_starts_with($path, '/storage/')) {
            $path = substr($path, strlen('/storage/'));
        }
        $path = urldecode($path);
        return LessonImage::where('path', $path)->first();
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
