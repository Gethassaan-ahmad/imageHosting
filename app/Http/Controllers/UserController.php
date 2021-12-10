<?php

namespace App\Http\Controllers;

use App\Http\Requests\loginRequest;
use App\Http\Requests\registerRequest;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Throwable;
use App\Models\User;
use App\Models\token;
use Illuminate\Support\Facades\Hash;
use App\Mail\sendmail;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    function createToken($data)

    {
        try {
            $key = config('constant.keyValue');
            $payload = array(
                "iss" => "http://127.0.0.1:8000",
                "aud" => "http://127.0.0.1:8000/api",
                "iat" => time(),
                "nbf" => 1357000000,
                "data" => $data,
            );
            $jwt = JWT::encode($payload, $key, 'HS256');
            return $jwt;
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }

    function emailToken($data)
    {
        try {
        date_default_timezone_set('Asia/Karachi');
        $issued_At = time() + 3600;
        $key = config('constant.keyValue');
        $payload = array(
            "iss" => "http://127.0.0.1:8000",
            "aud" => "http://127.0.0.1:8000",
            "iat" => time(),
            "exp" => $issued_At,
            "data" => $data,
        );
        $jwt = JWT::encode($payload, $key, 'HS256');
        return $jwt;
        }catch (Throwable $e) {
        return $e->getMessage();
    }
}

    public function register(registerRequest $request)

    {
        try {

            //Validate the fields
            $fields = $request->validated();
            $emailToken = $this->emailToken($request->email);
             $url = 'http://127.0.0.1:8000/api/userRoute/emailConfirmation/' . $emailToken . '/' . $request->email;
            //  Mail::to($request->email)->send(new sendMail($url, $request->name));
           
             //Create the user
            $user = User::create([
                'name' => $fields['name'],
                'email' => $fields['email'],
                'password' => Hash::make($fields['password']),
                'age' => $fields['age'],
                'image' => $request->file('image')->store('users_images'),
                'token' => $emailToken,
                // 'fatherName'=> $request
            ]);
            
            // dd($user);
            // Mail::to($request['email'])->send(new Sendmail());
            //Generate token for the user
            //$token = $user->createToken('image_hosting')->plainTextToken;

            $response = [
                'message' => 'User has been created successfully',
                'user' => $user,
                //'token' => $token
            ];

            //Return HTTP 201 status, call was successful and something was created
            return response($response, 201);
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }

    public function emailverify($hash , $email)
    {
        // dd($email);
        $user_exist = User::where('email', $email)->where('token', $hash)->first();
        // dd($user_exist->token);
    
        if ($user_exist->token != $hash) {
            return response([
                'message' => 'Unauthenticated',
            ]);
        }
        if(isset($user_exist)){
            $user_exist->email_verified_at = time();
            $user_exist->save();
            return response([
                'message' => 'Now your ImageHosting Account has been Verified',
            ]);
        }
    }

    public function login(loginRequest $request)
    {
        // dd('sda');
        try {
            $request = $request->validated();

            // Check Student
            $user = User::where('email', "=", $request['email'])->first();
            // dd($user->id);
            if (isset($user->id)) {

                if (Hash::check($request['password'], $user->password)) {
                    // dd(token::all());
                    $isLoggedIn = token::where('userID', $user->id)->first();
                    // dd($isLoggedIn);
                    // Create Token
                    $token = $this->createToken($user->id);
                    if ($isLoggedIn) {
                        return response([
                            "message" => "User already logged In",
                            'token' => $token,
                        ], 400);
                    }
                    

                    // dd($token);
                    // saving token table in db
                    $saveToken = token::create([
                        "userID" => $user->id,
                        "token" => $token
                    ]);
                    $response = [
                        'status' => 1,
                        'message' => 'Logged in successfully',
                        'user' => $user,
                        'token' => $token
                    ];

                    return response($response, 201);
                } else {
                    return response([
                        'message' => 'Invalid email or password'
                    ], 401);
                }
            } else {
                return response()->json([
                    "status" => 0,
                    "message" => "User not found"
                ], 404);
            }
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }
    public function logout(Request $request)
    {
        try {
            $getToken = $request->bearerToken();
            $keyValue = config('constant.keyValue');
            $decoded = JWT::decode($getToken, new Key($keyValue, "HS256"));
            $userID = $decoded->data;
            $userExist = Token::where("userID", $userID)->first();
            if ($userExist) {
                $userExist->delete();
            } else {
                return response([
                    "message" => "This user is already logged out"
                ], 404);
            }

            return response([
                "message" => "logout successfull"
            ], 200);
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }

    public function fetchUserProfile(Request $request)

    {
        $getToken = $request->bearerToken();
        $keyValue = config('constant.keyValue');
        $decoded = JWT::decode($getToken, new Key($keyValue, "HS256"));

        $uID = $decoded->data;
        $TokenExist = token::where('userID', $uID)->first();
        // dd("$TokenExist");
        if ($uID) {
            $profile = User::find($uID);
            return response([
                "Profile" => $profile
            ], 200);
        }
        if (!isset($TokenExist)) {
            return "token doesnot exist";
            return response([
                "mesage" => "token not exists"
            ]);
        } else {
            return $TokenExist;
        }
    }

    public function updateUserProfile(Request $request, $id){
        try {
            
            $user = User::all()->where('id', $id)->first();
            // if (!isset($request->email)) {
            //     $request->email = $request->email;
            //     $request->save();
                
            // }else {
            //     return([
            //         'message'=> 'email dose not change'
            // //     ]);
            // }
            if (isset($user)) {
                $user->update($request->all());

                return response([
                    'Status' => '200',
                    'message' => 'you have successfully Updated User Profile',
                ]);
            }
            if ($user == null) {
                return response([
                    'Status' => '404',
                    'message' => 'User not found',
                ]);
            }
        } catch (Throwable $e) {
            return $e->getMessage();
        }

        
    }



}

