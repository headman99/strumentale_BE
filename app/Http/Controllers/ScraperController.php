<?php

namespace App\Http\Controllers;

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
        $promises = [];
        $url = 'http://localhost:8800/api/scraper/scrape_pages';
        $surveys = Survey::get();
        /*$responses = Http::pool(function(Pool $pool) use ($surveys,$url){
            return collect($surveys)
                ->map(function ($survey) use ($pool,$url){
                    return $pool->get($url, [
                        'instrument' => $survey->text,
                    ])['tem_list'];
                });
        });*/
        /*try{
            $responses = Http::pool(function (Pool $pool) use ($url, $surveys) {
                    return collect($surveys) 
                        ->map(fn ($survey) =>
                            Http::get($url, [
                                'instrument' => 'Yamagha p',
                                'first' => true,
                            ])  
                        );
                });
                //$responses = json_encode($responses);
                return response($responses);
        }catch (\Exception $e) {
            // Handle the exception (e.g., log the error message)
            return $e->getMessage();
        }*/
        $promises = [];
        try {
            $client = new Client();

            $promises = [
                'response_1' => $client->getAsync($url, ['query' => ['instrument' => 'Yamaha p45', 'first' => true]]),
                'response_2' => $client->getAsync($url, ['query' => ['instrument' => 'Yamaha p20', 'first' => true]]),
            ];

            $results = [];
            $responses= [];
            foreach($promises as $promise){
                $promise->then(function($resp) use ($responses){
                    array_push($responses,$resp);
                });
            }


            foreach ($responses as $key => $response) {
                if ($response['state'] === 'fulfilled') {
                    $responseValue = $response['value'];
                    $results[$key] = json_decode($responseValue->getBody(), true);
                } else {
                    // Handle the case where a request failed
                    $results[$key] = ['error' => 'Request failed'];
                }
            }

            // Return the results in JSON format
            $results =  json_encode($results);
            return $results;
        } catch (\Exception $e) {
            // Handle the exception (e.g., log the error message)
            return $e->getMessage();
        }
    }
}
