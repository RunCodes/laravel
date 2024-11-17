<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ExportHandle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sql; //sql语句
    protected $fid; //文件ID
    protected $type;//导出的类型
    protected $fileName;//保存的文件名称

    /**
     * Create a new job instance.
     */
    public function __construct($sql, $fid, $type)
    {
        $this->sql = $sql;
        $this->fid = $fid;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        // Log::info("初始: ".memory_get_usage()."B\n");

        $this->fileName = $this->fid . '.' . ($this->type == 'excel' ? 'csv' : 'json');

        $dir = storage_path() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;

        $offset = 0;
        $limit = 10000;
        $first = true;
        while (true) {

            // Log::info("使用: ".memory_get_usage()."B\n");

            $array = [];
            $results = DB::select($this->sql . " limit ?, ? ", [$offset, $limit]);

            if (empty($results)) break;

            foreach ($results as $key => $val) {
                array_push($array, (array)$val);
            }

            $offset += $limit;


            if ($this->type == 'excel') {
                $head = $first ? array_keys(current($array)) : [];
                $this->exportToCsv($array, $dir . $this->fileName, [$head]);

                $first = false;
            } else if ($this->type == 'json') {
                $this->exportToJson($array, $dir . $this->fileName);
            }
        }

        Cache::put($this->fileName, 'success', 86400);
    }

    public function exportToCsv(array $data, string $filePath, ?array $head)
    {

        $fileHandle = fopen($filePath, 'a');

        if ($head) {
            foreach ($head as $row) {
                if (!fputcsv($fileHandle, $row)) {
                    fclose($fileHandle);
                }
            }
        }


        foreach ($data as $row) {
            if (!fputcsv($fileHandle, $row)) {
                fclose($fileHandle);
            }
        }
        fclose($fileHandle);
    }


    protected function exportToJson(array $data, string $filePath)
    {

        $fileHandle = fopen($filePath, 'a');

        // Log::info( "峰值: ".memory_get_peak_usage()."B\n");


        foreach ($data as $val) fwrite($fileHandle, json_encode($val) . PHP_EOL);

        fclose($fileHandle);
    }

}
