<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MpesaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); // WE ARE NOT CURRENTLY USING SANCTUM

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/mpesa/callback', [MpesaController::class, 'callback']); // Receives the receipt

// ==========================================
// PUBLIC ROUTES (Anyone can view these)
// ==========================================
Route::get('/groups', [GroupController::class, 'index']);
Route::get('/groups/{group}', [GroupController::class, 'show']);

// ==========================================
// PROTECTED ROUTES (Requires Passport Token)
// ==========================================
Route::middleware('auth:api')->group(function () {
    
    // --- User & Auth ---
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::get('/allUsers', [AuthController::class, 'getAllUsers']);
    Route::get('/admin/pending-sellers', [AuthController::class, 'getPendingSellers']);
    Route::post('/userEdit', [AuthController::class, 'updateUserData']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // --- Articles ---
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('showArticle/{id}', [ArticleController::class, 'show']);
    Route::post('/articles/store', [ArticleController::class, 'store']);
    Route::put('updateArticle/{id}', [ArticleController::class, 'update']);
    Route::delete('/articles/delete/{id}', [ArticleController::class, 'destroy']);
    
    // --- Groups Base CRUD ---
    // Handles store(), update(), and destroy() for the groups.
    // (Uses 'except' because index and show are public above)
    Route::apiResource('/groups', GroupController::class)->except(['index', 'show']);
    
    // --- Group Posts ---
    Route::post('/groups/{id}/posts', [GroupController::class, 'storePost']);
    Route::delete('/groups/{id}/posts/{postId}', [GroupController::class, 'destroyPost']); // Admin delete post
    
    // --- Group Membership (Student Actions) ---
    Route::post('/groups/{id}/toggleMembership', [GroupController::class, 'toggleMembership']);
    
    // --- Group Admin Dashboard Actions ---
    Route::get('/groups/{id}/members', [GroupController::class, 'getMembers']); // Fetch all members
    Route::put('/groups/{id}/members/{userId}', [GroupController::class, 'updateMemberRole']); // Promote/Demote
    Route::delete('/groups/{id}/members/{userId}', [GroupController::class, 'removeMember']); // Kick user

    Route::post('/groups/{id}/achievements', [GroupController::class, 'storeAchievement']);
    Route::put('/groups/{id}/achievements/{achievementId}', [GroupController::class, 'updateAchievement']); // <-- NEW
    Route::delete('/groups/{id}/achievements/{achievementId}', [GroupController::class, 'destroyAchievement']);
    // --- Group Milestones (Events & Achievements) ---
    Route::post('/groups/{id}/events', [GroupController::class, 'storeEvent']);
    Route::delete('/groups/{id}/events/{eventId}', [GroupController::class, 'destroyEvent']);
    Route::post('/donate/stkpush', [MpesaController::class, 'stkPush']);
    Route::get('/donate/status/{checkoutRequestId}', [MpesaController::class, 'checkTransactionStatus']);
});