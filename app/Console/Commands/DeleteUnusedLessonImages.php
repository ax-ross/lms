<?php

namespace App\Console\Commands;

use App\Models\LessonImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteUnusedLessonImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lesson-images:delete-unused';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all lesson images which have empty lesson id';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // FIXME delete only image what saved some time ago
        Storage::disk('public')->delete(LessonImage::whereNull('lesson_id')->pluck('path')->all());
        LessonImage::whereNull('lesson_id')->delete();
    }
}
