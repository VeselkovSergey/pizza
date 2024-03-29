<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'github'], function () {

    Route::post('/push', [\App\Http\Controllers\Api\GitHubController::class, 'Push'])
        ->name('github-push');

    Route::get('/test', [\App\Http\Controllers\Api\GitHubController::class, 'test']);

});

Route::post('telegram-bot-webhook', [App\Http\Controllers\TelegramBOT\TelegramBotController::class, 'Index'])->name('telegram-bot-webhook');
