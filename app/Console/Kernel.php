<?php

namespace App\Console;

use App\Http\ResultResource\ResultResource;
use App\Models\Result;
use App\Models\Survey;
use Carbon\Carbon;
use GuzzleHttp\Promise\Utils;
use Illuminate\Http\Client\Pool;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {

        $schedule->call(function () {
            try{
                $results = [];
                $url = 'http://localhost:8800/api/scraper/scrape_pages';
                $surveys = Survey::get();
                foreach ($surveys as $survey) {
                    $response =  Http::retry(2, 30000)->get($url, [
                        "instrument" => $survey->text,
                        "first" => true
                    ]);
                    $response = json_decode($response, false);
                    if (isset($response)) {
                        $results[] = [
                            'survey' => $survey->id,
                            'name' => $survey->text,
                            'price' => $response->price,
                            'url' => $response->url,
                            "created_at" => Carbon::now()->format('Y-m-d'),
                            "updated_at" => Carbon::now()->format("Y-m-d"),
                        ];
                    }
                }
                DB::table("result")->insert($results);
                
            }catch(\Exception $ex){
                $ex->getMessage();
            }
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
