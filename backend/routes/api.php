<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');// WE ARE NOT CURRENTLY USING SANCTUM
Route::post('/login',[AuthController::class,'login']);
Route::post('/register',[AuthController::class,'register']);

Route::middleware('auth:api')->group(function (){
    Route::get('/user',[AuthController::class,'getUser']);
    Route::get('/allUsers',[AuthController::class,'getAllUsers']);
    Route::get('/admin/pending-sellers', [AuthController::class, 'getPendingSellers']);
    Route::post('/userEdit',[AuthController::class,'updateUserData']);
    Route::post ('/logout',[AuthController::class,'logout']);
    Route::get('/articles',[ArticleController::class,'index']);
    Route::get('showArticle/{id}',[ArticleController::class,'show']);
    Route::post('/articles/store',[ArticleController::class,'store']);
    Route::put('updateArticle/{id}',[ArticleController::class,'update']);
    Route::delete('/articles/delete/{id}',[ArticleController::class,'destroy']);
});

