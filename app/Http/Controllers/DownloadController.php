<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;


class  DownLoadController extends Controller{

	protected $disk = 'public';


    public function  consult(Request $request){

        $fileName = $request->input('fileName');

        return response()->json([
            'message' => 'Processing, please wait a moment',
            'data' => Cache::get($fileName) ,
        ], (Cache::get($fileName) == 'success' ? 200 : 404 ));
    }


	public function download(Request $request){

		$fileName = $request->input('fileName');

        if (!Storage::disk($this->disk)->exists($fileName)) {
            return response()->json([
                'message' => 'file not found',
            ], 404);
        }

        $filePath = Storage::disk($this->disk)->path($fileName);
        //下载后删除文件
        return response()->download($filePath, $fileName)->deleteFileAfterSend(true); 
    }


}