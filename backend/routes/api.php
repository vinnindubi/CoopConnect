<?php

use App\Http\Controllers\ArticleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/articles',[ArticleController::class,'index']);
Route::get('showArticle/{id}',[ArticleController::class,'show']);
Route::post('/articles/store',[ArticleController::class,'store']);
Route::put('updateArticle/{id}',[ArticleController::class,'update']);
Route::delete('/articles/delete/{id}',[ArticleController::class,'destroy']);