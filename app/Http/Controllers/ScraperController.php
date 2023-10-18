<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;

class ScraperController extends Controller
{
    public function save_scrape_result(Request $request):Response
    {
        return response(['message' => 'ciao']);
    }
}
