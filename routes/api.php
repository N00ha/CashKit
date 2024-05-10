<?php

use App\Http\Controllers\GoalController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Public Routes
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/send_reset_password_email', [PasswordResetController::class, 'send_reset_password_email']);
Route::post('/reset_password/{token}', [PasswordResetController::class, 'reset']);

//Protected Routes
Route::middleware('auth:sanctum')->group(function ()
{
    //Goal Routes
    Route::post('/goal',[GoalController::class, 'store']);
    Route::put('/goals/{goal_id}',[GoalController::class, 'update']);
    Route::get('goals',[GoalController::class, 'show']);
    Route::delete('/goals/{goal_id}',[GoalController::class, 'destroy']);
    //Questions Routes
    Route::get('/questions/', [QuestionController::class,'show']);
    Route::post('/questions', [QuestionController::class,'store']);
    Route::put('/questions/', [QuestionController::class, 'update']);
    Route::delete('/questions/', [QuestionController::class, 'destroy']);

    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/changePassword', [UserController::class, 'change_password']);
});





