<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScraperController extends Controller
{
    public function save_scrape_result(Request $request):JsonResponse
    {
        return response()->json($request->data);
    }
}
