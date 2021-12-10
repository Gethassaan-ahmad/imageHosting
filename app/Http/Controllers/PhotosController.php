<?php

namespace App\Http\Controllers;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\token;
use App\Models\Photos;
use Illuminate\Http\Request;

class PhotosController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'name'=> 'required|string',
            'image'=> 'required|mimes:jpg,png,jpeg,gif,svg',
            // 'status'=>'required'
        ]);
        // dd($request->image);


         $getToken = $request->bearerToken();
            $keyValue = config('constant.keyValue');
            $decoded = JWT::decode($getToken, new Key($keyValue, "HS256"));
            $userID = $decoded->data;
            $userExist = token::where("userID", $userID)->first();
            $image = $request->image;
            // dd($image); 
            if ($userExist) {
                //  $request->file('image')
                if (isset($image)) {
                    // dd('sdf');
                    //make a path to store image
                    // $destinationPath = 'C:/xampp/htdocs/Laravel/Project/storage/app/users_images';
                    // dd($destinationPath);
                    $extension=$image->getClientOriginalExtension();
                    // dd($extension);
                    //change the image name for no duplication of same name
                    $name = $image->getClientOriginalName();
                    // dd($name);
                    //store file in a provided path
                    $image->store('user_images');
                    // dd($destinationPath);
                    $destination = 'C:/xampp/htdocs/Laravel/Project/storage/app/user_images/' . $name;

                    // dd($image);
        
                }
              $upload =  Photos::create([
                        'userID'=>$userID,
                    'name'=>$name,
                    'image'=> $destination,
                    'extension'=> $extension,
                    'privacy'=>$request->privacy
                    ]);
                //  $upload->save();
                 return response([
                    "message" => "image upload successfully"
                ], 200);
            } else {
                return response([
                    "message" => "This user is already logged out"
                ], 404);
            }
    }
    

        public function deleteImage(Request $request ,$id)
    {

        $getToken = $request->bearerToken();
        $keyValue = config('constant.keyValue');
        $decoded = JWT::decode($getToken, new Key($keyValue, "HS256"));
        $userID = $decoded->data;
        $userExist = Photos::where("userID", $userID)->first();
        if ($userExist) {
        if (Photos::where('id', '=', $id)->delete($id)) {
            return response([
                'Status' => '200',
                'message' => 'Image Deleted successfully'
            ], 200);
        } else {
            return response([
                'message' => 'Not Found.'
            ], 404);
        }
    }
    }

    public function listofPhotos(Request $request)
    {
        //call a helper function to decode user id
        // $userID = DecodeUser($request);
        $getToken = $request->bearerToken();
        $keyValue = config('constant.keyValue');
        $decoded = JWT::decode($getToken, new Key($keyValue, "HS256"));
        $userID = $decoded->data;
        $userExist = token::where("userID", $userID)->first();
        if ($userExist) {
        $user_images = Photos::all()->where('userID', $userID);
            // dd($my_images);
        if (json_decode($user_images) == null) {
            return response(['Images' => 'No Image'], 404);
        }
        //message on Successfully
        if ($user_images) {
            return response(['Images' => $user_images], 200);
        }
    }
    }

    public function searchImages(Request $request)
    {
        $find = $request->name;

        $photos = Photos::where('privacy', 'public')->where('name', 'LIKE', '%' . $find . '%')->orWhere('address', 'LIKE', '%' . $find . '%')->orWhere('privacy', 'LIKE', '%' . $find . '%')->get();
        // dd($photos);
        if (count($photos) > 0)
            return response(['Photos' => $photos], 200);
        else {
            return response(['Photos' => 'No Details found']);
        }
    }


    
}
