<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Result;
use App\Models\Survey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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
            $surveys = Survey::where("user", $request->user()->id)->limit(150)->orderBy("created_at", "desc")->get();
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
            "title" => ["string","max:100"]
        ]);

        try {
            Survey::find($request->id)->update(['title'=> $request->title]);
            return response()->noContent();
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    /* ITEM TABLE REMOVED
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
                throw ValidationException::withMessages(['error' => 'Parametri non validi']);

            if ($request->id) {
                $item = Item::find($request->id);
                if (!$item)
                    throw ValidationException::withMessages(['error' => 'Parametri non validi']);
                $user = Survey::find($item->survey)->user;
                if ($user == $request->user()->id)
                    $item->delete();
                else
                    throw ValidationException::withMessages(['error' => 'Parametri non validi']);
            }


            if ($request->survey) {
                $user = Survey::find($request->survey)->user;
                if ($user == $request->user()->id)
                    Item::where("survey", $request->survey)->delete();
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
        $validate = $request->validate([
            "survey" => ["integer", "nullable", "sometimes"]
        ]);

        try {

            if ($id) {
                $item = Item::find($request->id);
                if (!$item)
                    return response()->json(null);
                $user = Survey::find($item->id)->user;
                if (!($user == $request->user()->id))
                    throw ValidationException::withMessages(['error' => 'Parametri non validi']);
                return response()->json($item);
            }

            if ($request->survey) {
                $user = Survey::find($request->survey)->user;
                if ($user == $request->user()->id)
                    return response()->json(Item::where("survey", $request->survey)->limit(150)->orderBy("created_at", "desc")->get());
                else
                    throw ValidationException::withMessages(['error' => 'Parametri non validi']);
            }

            $items = DB::table("item")
                ->join('survey', 'survey.id', '=', "item.id")
                ->select("item.*")
                ->where("survey.user", '=', $request->user()->id)
                ->get();

            return response()->json($items);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }
    */

    public function save_result(Request $request): Response
    {
        $validate = $request->validate([
            //'item' => ["required", "integer"],
            'name' => ["nullable", "sometimes", "string", 'max:250'],
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

    public function get_result(Request $request, string $id = null): JsonResponse
    {
        $validate = $request->validate([
            //"id" => ["integer", "nullable", "sometimes"],
            //"item" => ["integer", "nullable", "sometimes"],
            "survey" => ["integer", "nullable", "sometimes"],
            "latest" => ["boolean", 'nullable', "sometimes"],
            "cheapest" => ["boolean", "nullable", "sometimes"]
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
            if ($request->cheapest)
                //complete
            $result = Result::where("survey", $request->survey)->get();
            return response()->json($result);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }
}
