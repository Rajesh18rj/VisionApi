<?php

namespace App\Http\Middleware;

use App\Http\Model\user;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class APIToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->header('Authorization')) {

                $user = DB::table('user')->where('api_token', $request->header('Authorization'))->first();
                if ($user) {
                    $userArray = (array) $user; // Convert stdClass to array
                    $users = new user($userArray); // Create an Eloquent model instance
                    Auth::login($users);
                    // Auth::login($user);
                    Auth::loginUsingId($user->id);
                    return $next($request);
                } else {
                    return response(["Type" => "E", "Message" => "Not a valid API request."]);
                }

        } else {
            Auth::loginUsingId(1);
            return $next($request);
            return response(["Type" => "E", "Message" => "Not a valid API request."]);
        }
    }
}
