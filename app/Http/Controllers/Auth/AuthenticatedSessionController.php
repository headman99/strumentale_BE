<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request) #: Response
    {
        $request->authenticate();

        $request->session()->regenerate();
        Cookie::queue('loggedin', 'True', 60*24*365*10, null, null, null, true, false, 'None');
        $accessToken = $request->user()->createToken('authToken', expiresAt: now()->addDay())->plainTextToken;

        #return response()->noContent();
        return response(["token" => $accessToken]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Cookie::queue(Cookie::forget('laravel_session'));
        $request->user()->tokens()->delete();
        $request->session()->invalidate();
        Auth::guard('web')->logout();
        $request->session()->regenerateToken();
        return response()->noContent();
    }
}
