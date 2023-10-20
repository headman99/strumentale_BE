<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\Survey;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\Utils;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class ScraperController extends Controller
{
    public function save_scrape_result(Request $request)
    {
        try{

        }catch(\Exception $ex){
            
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
