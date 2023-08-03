<?php

namespace App\Console\Commands;

use App\Models\Image;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteUnusedImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-unused-images';

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
        $unusedImages = Image::query()->whereNull('imageable_id')
            ->whereDate('created_at', '<', Carbon::now()->subDays(2));

        Storage::disk('public')->delete($unusedImages->pluck('path')->all());
        $unusedImages->delete();
    }
}
