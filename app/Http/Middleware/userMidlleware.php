<?php

namespace App\Http\Middleware;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\Token;
use Closure;
use Illuminate\Http\Request;

class userMidlleware
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
        $keyValue = config('constant.keyValue');
        $decoded = JWT::decode($getToken, new Key($keyValue,"HS256"));
        // dd($decoded);
        $uID = $decoded->data;

        $TokenExist = token::where('userID',$uID)->first();
        // dd($TokenExist);
        if (!isset($TokenExist)) {
            // return "token doesnot exist";
            return response([
                "mesage" => "token not exists"
            ]);
    }else{
        return $next($request);
    }
    
}
}