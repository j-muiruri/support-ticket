<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\CommentController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/v1/auth/register', [AuthController::class, 'register']);
Route::post('/v1/auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/v1/auth/logout', [AuthController::class, 'logout']);
    
    // Tickets
    Route::get('/v1/tickets', [TicketController::class, 'index']);
    Route::post('/v1/tickets', [TicketController::class, 'store']);
    Route::get('/v1/tickets/{id}', [TicketController::class, 'show']);
    
    // Admin only
    Route::middleware('admin')->group(function () {
        Route::patch('/v1/admin/tickets/{id}', [TicketController::class, 'update']);
    });
    
    // Comments
    Route::post('/v1/tickets/{ticketId}/comments', [CommentController::class, 'store']);
});