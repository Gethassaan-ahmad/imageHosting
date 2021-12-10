<?php

namespace App\Http\Middleware;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\Token;
use Closure;
use Illuminate\Http\Request;

class projectMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $getToken = $request->bearerToken(); 
        dd($getToken);
        $keyValue = config('constant.keyValue');
        $decoded = JWT::decode($getToken, new Key($keyValue,"HS256"));
        $uID = $decoded->id;

        $TokenExist = token::where('userID',$uID)->first();

        if (!isset($TokenExist)) {
            // return "token doesnot exist";
            return response([
                "mesage" => "token not exists"
            ]);
            
        }else {
                return $next($request);
        }
    }
}
