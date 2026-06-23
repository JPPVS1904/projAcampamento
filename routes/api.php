<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CampingController;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\MaritalStatusController;
use App\Http\Controllers\Api\V1\SectorController;
use App\Http\Controllers\Api\V1\SelectionMethodController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\ActivityController;
use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\AnswerController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\OptionController;
use App\Http\Controllers\Api\V1\PreRegistrationController;
use App\Http\Controllers\Api\V1\QuestionController;
use App\Http\Controllers\Api\V1\SectionController;
use App\Http\Controllers\Api\V1\SubscriptionController;
use App\Http\Controllers\Api\V1\RaffleController;
use App\Models\PreRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::model('subscription', PreRegistration::class);

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('users', [UserController::class, 'store'])->name('users.store');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/user', fn (Request $request) => $request->user())->name('user');

        Route::apiResource('marital-statuses', MaritalStatusController::class);
        Route::apiResource('selection-methods', SelectionMethodController::class);
        Route::apiResource('sectors', SectorController::class);
        Route::apiResource('campings', CampingController::class);
        Route::apiResource('events', EventController::class);
        Route::apiResource('users', UserController::class)->except('store');
        
        // New CakePHP replicated routes
        Route::apiResource('activities', ActivityController::class);
        Route::apiResource('addresses', AddressController::class);
        Route::apiResource('answers', AnswerController::class);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('options', OptionController::class);
        Route::apiResource('pre-registrations', PreRegistrationController::class);
        Route::apiResource('questions', QuestionController::class);
        Route::apiResource('sections', SectionController::class);
        Route::apiResource('subscriptions', SubscriptionController::class);

        // Raffle routes
        Route::get('raffles', [RaffleController::class, 'index'])->name('raffles.index');
        Route::post('raffles/{activity}/campers', [RaffleController::class, 'raffleCampers'])->name('raffles.campers');
    });
});