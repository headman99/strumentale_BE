<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;

class AllowCrawler
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
       /* $allowedHosts = ["http://localhost","http://crawler.strumentale.it","https://crawler.strumentale.it"];
        //$allowedHosts = ['none'];
        $requestHost = parse_url($request->headers->get('origin'),  PHP_URL_HOST);
        $verify = in_array($requestHost,$allowedHosts);
        if(!$verify){
            $requestInfo = [
                'host' => $requestHost,
                'ip' => $request->getClientIp(),
                'url' => $request->getRequestUri(),
                'agent' => $request->header('User-Agent'),
            ];
            throw new SuspiciousOperationException('This host is not allowed');
            //return response($request->header(),500);
           
        }*/
            
        return $next($request);
    }
}
