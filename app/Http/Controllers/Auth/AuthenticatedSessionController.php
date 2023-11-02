<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request) #: Response
    {
        try {
            $request->authenticate();

            $request->session()->regenerate();
            Cookie::queue('loggedin', 'True', 60 * 24 * 365 * 10,);
            $accessToken = $request->user()->createToken('authToken', expiresAt: now()->addDay())->plainTextToken;

            #return response()->noContent();
            return response(["status" => true,  "accessToken" => $accessToken]);
        } catch (\Exception $exc) {
            $message = $exc->getMessage();
            return response(["status" => false, "exception" => $message,"message" => "Credenziali non valide"], \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Cookie::forget('laravel_session');
        Cookie::forget('loggedin');
        $request->user()->tokens()->delete();
        $request->session()->invalidate();
        Auth::guard('web')->logout();
        $request->session()->regenerateToken();
        return response()->noContent();
    }
}
