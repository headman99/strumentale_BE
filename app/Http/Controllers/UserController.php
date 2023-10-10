<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Result;
use App\Models\Survey;
use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleHttpClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use \Symfony\Component\Panter\Client;


class UserController extends Controller
{
    public function save_survey(Request $request): Response
    {
        $validate = $request->validate([
            'title' => ["nullable", "sometimes", "string", "max:100"],
            "text" => ['required', "string", "max:400"],

        ]);

        try {

            //Count how many survey there are for a certain user
            $surveys =  Survey::where("user", $request->user()->id)->get();
            if ($surveys->count() >= 20)
                throw ValidationException::withMessages(['error' => 'Non puoi salvare più di 20 ricerche.']);

            Survey::create(array_merge(
                $validate,
                [
                    "user" => $request->user()->id
                ]
            ));
            return response()->noContent();
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => "Verifica che la ricerca non esista già tra le Ricerche salvate."], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function delete_survey(Request $request): Response
    {
        $validate = $request->validate([
            "id" => ["sometimes", "integer"],
        ]);

        try {
            if (!$request->id) {
                Survey::where("user" . $request->user()->id)->delete();
                return response()->noContent();
            }

            Survey::find($request->id)->delete();
            return response()->noContent();
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function get_survey(Request $request, string $id = null): JsonResponse
    {
        /*$validate = $request->validate([
            "id" => ["integer", "sometimes", "nullable"],
        ]);*/

        try {
            if ($id) {
                return response()->json(Survey::where(
                    [
                        ["user", $request->user()->id],
                        ["id", $id]
                    ]
                )->first());
            }
            $surveys = Survey::where("user", $request->user()->id)->limit(20)->orderBy("created_at", "desc")->get();
            return response()->json($surveys);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function update_survey(Request $request, string $id = null)
    {
        $validate = $request->validate([
            "id" => ["integer", "sometimes", "required"],
            "title" => ["string", "max:100"]
        ]);

        try {
            Survey::find($request->id)->update(['title' => $request->title]);
            return response()->noContent();
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }


    public function save_item(Request $request): Response
    {
        $validate = $request->validate([
            "name" => ["required", "string"],
            "url" => ["required", "string"],
            "img" => ["sometimes", 'nullable']
        ]);

        try {
            $items =  Item::where("user", $request->user()->id)->get();
            if ($items->count() >= 20)
                throw ValidationException::withMessages(['error' => 'Non puoi salvare piu di 20 oggetti.']);

            Item::create(array_merge(
                $validate,
                ["user" => $request->user()->id]
            ));
            return response()->noContent();
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }


    public function delete_item(Request $request): Response
    {
        $validate = $request->validate([
            "id" => ["integer", "nullable", "sometimes"]
        ]);

        try {
            if (!$request->id)
                Item::where("user", $request->user()->id)->delete();

            if ($request->id) {
                $item = Item::find($request->id);
                if (!$item)
                    throw ValidationException::withMessages(['error' => 'Parametri non validi']);
                if ($item->user == $request->user()->id)
                    $item->delete();
                else
                    throw ValidationException::withMessages(['error' => 'Parametri non validi']);
            }

            return response()->noContent();
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function get_item(Request $request, string $id = null): JsonResponse
    {
        try {
            $items = null;
            if ($id) {
                $item = Item::find($request->id);
                if (!$item)
                    throw ValidationException::withMessages(['error' => 'Parametri non validi']);

                if (!$item->user == $request->user()->id)
                    throw ValidationException::withMessages(['error' => 'Parametri non validi']);
                $items = $item;
            } else {
                $items = Item::where("user", $request->user()->id)->get();
            }

            return response()->json($items);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }


    public function save_result(Request $request): Response
    {
        $validate = $request->validate([
            //'item' => ["required", "integer"],
            'name' => ["required", "string", 'max:250'],
            'survey' => ["required", "integer"],
            'price' => ["required", "integer"],
            'url' => ["required", "string"]
        ]);

        try {
            Result::create($validate);
            return response()->noContent();
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function delete_result(Request $request): Response
    {
        $validate = $request->validate([
            "id" => ["integer", "nullable", "sometimes"],
            "survey" => ["integer", "nullable", "sometimes"]
            //"item" => ["integer", "nullable", "sometimes"]
        ]);

        try {
            if (!$request->id && !$request->survey)
                throw ValidationException::withMessages(['error' => 'Parametri non validi']);

            if ($request->id) {
                $result = Result::find($request->id);
                if (!$result)
                    throw ValidationException::withMessages(['error' => 'Parametri non validi']);
                $user = Survey::find($result->survey)->user;
                if ($user != $request->user()->id)
                    throw ValidationException::withMessages(['error' => 'Parametri non validi']);
                $result->delete();
            }


            if ($request->survey) {
                $user = Survey::find($request->survey)->user;
                if ($user != $request->user()->id)
                    throw ValidationException::withMessages(['error' => 'Parametri non validi']);
                Result::where("survey", $request->survey)->delete();
            }

            return response()->noContent();
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function get_result(Request $request, string $id = null) //: JsonResponse
    {
        $validate = $request->validate([
            //"id" => ["integer", "nullable", "sometimes"],
            //"item" => ["integer", "nullable", "sometimes"],
            "survey" => ["integer", "nullable", "sometimes"],
            "latest" => ["boolean", 'nullable', "sometimes"],
            "cheapest" => ["boolean", "nullable", "sometimes"],
            "samples" => ["sometimes", 'nullable', "integer"],
            //nel formato Y-m-d
            "n_months" => ["sometimes", "nullable", 'integer']
        ]);

        try {

            if (!$id && !$request->survey)
                throw ValidationException::withMessages(['error' => 'Parametri non validi']);
            if ($id) {
                $result = Result::find($id);
                if (!(Survey::find($result->id)->user == $request->user()->id))
                    throw ValidationException::withMessages(['error' => 'Parametri non validi']);
                return response()->json($result);
            }

            $user = Survey::find($request->survey)->user;
            if (!($user == $request->user()->id))
                throw ValidationException::withMessages(['error' => 'Parametri non validi']);
            if ($request->latest)
                //complete
                if ($request->cheapest) {
                }
            //complete

            $results = array();
            if ($request->samples) {
                //default 3 months
                $n_months = $request->n_months !== null ? $request->n_months : 3;
                $today = Carbon::today();
                $endDate = Carbon::today()->subMonths($n_months);
                $all_results = Result::where([
                    ["survey", $request->survey],
                    ["created_at", '<=', $today],
                    ["created_at", ">=", $endDate]
                ])->orderBy("created_at", 'asc')->select("id", "created_at", "price")->get();
                $all_results_count = $all_results->count();
                $skip = round($all_results_count / $request->samples);
                $results = $all_results->filter(function ($item, $index) use ($skip, $all_results_count) {

                    return (($index % ($skip)) == 0) || ($index == $all_results_count - 1);
                })->values();
            } else {
                $results = Result::where("survey", $request->survey)->get();
            }

            return response()->json($results);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function scrapePage()
    {
        $path = str_replace("app/Http/Controllers", 'vendor/bin/drivers/chromedriver.exe',str_replace("\\","/",__DIR__));
        $client = \Symfony\Component\Panther\Client::createChromeClient($path);
        $client->request('GET', 'https://api-platform.com'); // Yes, this website is 100% written in JavaScript
        $client->clickLink('Getting started');

        // Wait for an element to be present in the DOM (even if hidden)
        $crawler = $client->waitFor('.doc');
        // Alternatively, wait for an element to be visible
        //$crawler = $client->waitForVisibility('#installing-the-framework');

        $text= $crawler->filter('.doc')->text();       //$client->takeScreenshot('screen.png'); // Yeah, screenshot!*/
        return response()->json($text);
    }
}
