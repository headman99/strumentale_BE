<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //in order to make CSRF token works you need to  eliminate both the value in the array and move the frontend app in the same subdomain of the backend app otherwhise laravel is not able to share cookies ascross different domains
        
    ];
}
