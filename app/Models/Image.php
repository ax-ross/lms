<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ['path', 'imageable_type', 'imageable_id'];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function findImageByAbsolutePath(string $absolutePath)
    {
        $path = parse_url($absolutePath, PHP_URL_PATH);
        if (str_starts_with($path, '/storage/')) {
            $path = substr($path, strlen('/storage/'));
        }
        $path = urldecode($path);
        return Image::where('path', $path)->first();
    }
}
