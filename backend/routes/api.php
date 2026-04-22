<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\MarketplaceController;
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

Route::get('/events', [EventController::class, 'index']);

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

    //Home feed 
    Route::get('student/feed', [StudentDashboardController::class, 'getFeed']);
    Route::get('home/upcoming', [StudentDashboardController::class, 'getHomeData']);
    Route::get('student/events/upcoming', [StudentDashboardController::class, 'getUpcomingEvents']);
    Route::get('student/marketplace/preview', [StudentDashboardController::class, 'getMarketplacePreview']);
    
    // --- Articles ---
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('showArticle/{id}', [ArticleController::class, 'show']);
    Route::post('/articles/store', [ArticleController::class, 'storeArticle']);
    Route::put('updateArticle/{id}', [ArticleController::class, 'update']);
    Route::delete('/articles/delete/{id}', [ArticleController::class, 'destroy']);
    // Fetch articles for a specific user
    Route::get('/users/{id}/articles', [ArticleController::class, 'userArticles']);
    
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
    Route::put('/groups/{id}/events/{eventId}', [GroupController::class, 'updateEvent']);
    Route::delete('/groups/{id}/events/{eventId}', [GroupController::class, 'destroyEvent']);
    Route::post('/donate/stkpush', [MpesaController::class, 'stkPush']);
    Route::get('/donate/status/{checkoutRequestId}', [MpesaController::class, 'checkTransactionStatus']);
    // Create a Global Campus Event
    Route::post('/admin/events', [EventController::class, 'storeGlobalEvent']);
    Route::delete('/admin/events/{id}', [EventController::class, 'destroyGlobalEvent']);
    // Toggle Event Featured Status
    Route::patch('/admin/events/{id}/feature', [EventController::class, 'toggleFeatureStatus']);
    // need to update this eventually and input role based access whether admin , or member ...

    Route::get('/announcements', [AnnouncementController::class, 'index']); // Public for students
    Route::post('/admin/announcements', [AnnouncementController::class, 'store']); // Admin only
    Route::patch('/admin/announcements/{id}/deactivate', [AnnouncementController::class, 'deactivate']); // Admin only


    //marketplace
    Route::get('/marketplace', [MarketplaceController::class, 'index']);
    Route::get('/sellers/{id}', [MarketplaceController::class, 'show']);
});