<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class DeleteOldFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-old-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $directory = storage_path('app/public');
        $files = File::allFiles($directory);

        $now = Carbon::now();

        foreach ($files as $file) {
            $extension = $file->getExtension();

            if (in_array($extension, ['csv', 'json','xlsx']) &&  $now->diffInDays( date('Y-m-d H:i:s',$file->getMTime() ) ) > 1) {
                // 删除文件
                File::delete($file);
                $this->info("Deleted file: {$file->getFilename()}");
            }
        }
    }
}
