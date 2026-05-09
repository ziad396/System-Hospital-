<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DoctorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user=Auth::user();
        if($user->role!='doctor') {
            # code...
            // if ($user->doctor()) {
            //     # code...
            // }
            return response()->json([
                'message'=>'you can not access here '
            ]);
            
        }
        return $next($request);
    }
}
