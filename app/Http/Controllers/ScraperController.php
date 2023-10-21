<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\Survey;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\Utils;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


class ScraperController extends Controller
{
    public function save_scrape_result(Request $request)
    {
        try{
            $data = array_map(function ($result) {
                return [
                    'name' => $result["name"],
                    "survey" =>$result["survey"],
                    "price" => $result["price"],
                    "url" => $result['url'],
                    "created_at"=> Carbon::now(),
                    "updated_at" => Carbon::now()
                ];
            },$request->data);
            DB::table("result")->insert($data);
            return response(["status"=>true,"message" => 'everything is great']);
            //return response($request->data);
        }catch(\Exception $ex){
            return response($ex->getMessage());
        }
    }

    public function get_surveys(Request $request)
    {
        try{
            $surveys = Survey::get();
            return response($surveys);
        }catch(\Exception $ex){
            $ex->getMessage();
        }
    }

    public function delete_old_results(Request $request){
        try{
            Result::whereDate('created_at','<=',now()->subMonths(3)->delete());
        }catch(\Exception $ex){
            $ex->getMessage();
        }
    }
}
