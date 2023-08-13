<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Result;
use App\Models\Survey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function save_survey(Request $request): Response
    {
        $validate = $request->validate([
            'title' => ["nullable", "sometimes", "string", "max:100"],
            "text" => ['required', "string", "max:400"],
        ]);

        try {
            Survey::create(array_merge(
                $validate,
                [
                    "user" => $request->user()->id
                ]
            ));
            return response()->noContent();
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function delete_survey(Request $request): Response
    {
        $validate = $request->validate([
            "id" => ["required", "integer"],
            "all" => ["boolean", "nullable", "sometimes"]
        ]);

        try {
            if ($request->all) {
                Survey::where("user" . $request->user()->id)->delete;
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
            $surveys = Survey::where("user", $request->user()->id)->limit(150)->orderBy("created_at", "desc")->get();
            return response()->json($surveys);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }


    public function save_item(Request $request): Response
    {
        $validate = $request->validate([
            'name' => ["required", "string", "max:200"],
            "survey" => ['required', "integer"],
        ]);

        try {
            Item::create($validate);
            return response()->noContent();
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }


    public function delete_item(Request $request): Response
    {
        $validate = $request->validate([
            "id" => ["integer", "nullable", "sometimes"],
            "survey" => ["integer", "nullable", "sometimes"]
        ]);

        try {
            if (!$request->id && !$request->survey)
                throw "Parametri API non validi, passarne almeno uno";

            if ($request->id) {
                $item = Item::find($request->id);
                if(!$item)
                    throw "Parametri non validi";
                $user = Survey::find($item->survey)->user;
                if ($user == $request->user()->id)
                    $item->delete();
                else
                    throw  "Parametri non validi";
            }


            if ($request->survey) {
                $user = Survey::find($request->survey)->user;
                if ($user == $request->user()->id)
                    Item::where("survey", $request->survey)->delete();
                else
                    throw  "Parametri non validi";
            }

            return response()->noContent();
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function get_item(Request $request): JsonResponse
    {
        $validate = $request->validate([
            "id" => ["integer", "nullable", "sometimes"],
            "survey" => ["integer", "nullable", "sometimes"]
        ]);

        try {
            if (!$request->id && !$request->survey)
                throw "Parametri API non validi, passarne almeno uno";

            if ($request->survey) {
                $user = Survey::find($request->survey)->user;
                if ($user == $request->user()->id)
                    return response()->json(Item::where("survey", $request->survey)->limit(150)->orderBy("created_at", "desc")->get());
                else
                    throw "Parametri API non validi";
            }

            $item = Item::find($request->id);
            if (!$item)
                return response()->json(null);
            $user = Survey::find($item->id)->user;
            if (!($user == $request->user()->id))
                throw "Parametri API non validi";
            return response()->json($item);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function save_result(Request $request): Response
    {
        $validate = $request->validate([
            'item' => ["required", "string", "max:200"],
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
            "item" => ["integer", "nullable", "sometimes"]
        ]);

        try {
            if (!$request->id && !$request->item)
                throw "Parametri API non validi, passarne almeno uno";

            if ($request->id) {
                $result = Result::find($request->id);
                if(!$result)
                    throw "Parametri API non validi";
                $user = Survey::find(Item::find($result->item)->survey)->user;
                if ($user == $request->user()->id)
                    $result->delete();
                else
                    throw  "Parametri non validi";
            }


            if ($request->item) {
                $user = Survey::find(Item::find($request->item)->survey)->user;
                if ($user == $request->user()->id)
                    Result::where("item", $request->item)->delete();
                else
                    throw  "Parametri non validi";
            }

            return response()->noContent();
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function get_result(Request $request): JsonResponse
    {
        $validate = $request->validate([
            "id" => ["integer", "nullable", "sometimes"],
            "item" => ["integer", "nullable", "sometimes"],
            "latest" => ["boolean", 'nullable', "sometimes"],
            "cheapest" => ["boolean", "nullable", "sometimes"]
        ]);

        try {
            if (!$request->id && !$request->item)
                throw "Parametri API non validi, passarne almeno uno";

            if ($request->item) {
                $user = Survey::find(Item::find($request->item)->survey)->user;
                if ($user == $request->user()->id){
                    if($request->latest)
                        return response()->json(Result::where("item", $request->item)->orderBy("created_at", "desc")->first());
                    if($request->cheapest)
                        return response()->json(Result::where("item", $request->item)->orderBy("price", "asc")->first());
                    
                    return response()->json(Result::where("item", $request->item)->limit(150)->orderBy("created_at", "desc")->get());
               } else
                    throw "Parametri API non validi";
            }

            $result = Result::find($request->id);
            if (!$result)
                return response()->json(null);
            $user = Survey::find(Item::find($result->item)->survey)->user;
            if (!($user == $request->user()->id))
                throw "Parametri API non validi";
            return response()->json($result);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

}
