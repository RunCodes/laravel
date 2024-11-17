<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SqlServices;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\HistorySql;
use App\Jobs\ExportHandle;

use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class ExecuteHandleController extends Controller
{
    protected $SqlServices;
 
    public function __construct(SqlServices $SqlServices)
    {
        $this->SqlServices = $SqlServices;
    }

	public function execute(Request $request){

        $sql    = $request->input('sql',null);
        $page   = $request->input('page',0);
        $limit  = $request->input('limit',20); 
        $page   = ($page == 0 ? 0 : $page - 1 ) * $limit;

        $check  = $request->input('check',false); //点击分页的时候会带上来，这时候不会记录sql
        $exportType = $request->input('exportType',null); 

        if (($validResult = $this->SqlServices->validate($sql)) != 'success') {
            return response()->json([
            	'message' => $validResult
            ], 500);
        }	

        if(!$check){
	        $record = new HistorySql();
            $record->user_id = Auth::id();
	        $record->sql     = $sql;
        }

        try {
            $results = DB::select($sql . " limit ?, ? ",[$page,$limit] );
            $check ?: $record->save();

            if($exportType){
                $fid = uniqid(); //文件ID
                ExportHandle::dispatch($sql, $fid, $exportType);
            } 
            return response()->json([
                'fid'=> isset($fid) ? $fid.'.'.($exportType == 'excel' ?'csv':'json') : '',
            	'data'  => $results,
            	'total' => DB::select($this->SqlServices->editSql($sql))[0]->total
            ],200);

        } catch (\Exception $e) {

        	if(!$check){
            	$record->error = $e->getMessage();
            	$record->save();
        	}

        	//返回错误信息
            return response()->json([
            	'message' => 'Error executing SQL: ' . $e->getMessage()
            ], 500);
        }
	}

}