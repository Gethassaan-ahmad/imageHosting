<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhotosController;

Route::post("/register", [UserController::class, "register"]);
Route::post("/login", [UserController::class, "login"]);
Route::get('/emailConfirmation/{token}/{email}', [UserController::class, 'emailverify']);

Route::post("/forgot", [UserController::class, "forgotPassword"]);


Route::middleware(['protectedpage'])->group (function()
{
    Route::post("/logout", [UserController::class, "logout"]);
    Route::get("/fetchdata", [UserController::class, "fetchUserProfile"]);
    Route::post("/updatedata/{id}", [UserController::class, "updateUserProfile"]);


});

    Route::post("/uploadPhoto", [PhotosController::class, "upload"]);
    Route::delete("/deletPhoto/{id}", [PhotosController::class, "deleteImage"]);
    Route::get("/listallPhotos", [PhotosController::class, "listofPhotos"]);
    Route::get("/searchImages", [PhotosController::class, "searchImages"]);



