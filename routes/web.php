<?php

use Illuminate\Support\Facades\Route;

Auth::routes();
Route::get('/', function () { return redirect('chat'); });


Route::group(
    ['middleware' => ['auth']], function() {
        Route::get('/chat', [\App\Http\Controllers\ChatController::class, 'index']);
        Route::post('/chat', [\App\Http\Controllers\ChatController::class, 'create']);
        Route::get('/chat/{chatId}', [\App\Http\Controllers\ChatController::class, 'chat']);
        Route::get('/chat/{chatId}/messages', [\App\Http\Controllers\ChatController::class, 'messages']);
        Route::post('/chat/add-message', [\App\Http\Controllers\ChatController::class, 'addMessage']);

        Route::put('/user/{user}/online', [\App\Http\Controllers\UserStatusController::class, 'online']);
        Route::put('/user/{user}/offline', [\App\Http\Controllers\UserStatusController::class, 'offline']);
        Route::get('/chats', [\App\Http\Controllers\ChatController::class, 'chats']);
        // test
        Route::get('/user/{user}/online', [\App\Http\Controllers\UserStatusController::class, 'online']);
        Route::get('/user/{user}/offline', [\App\Http\Controllers\UserStatusController::class, 'offline']);
    }
);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/test', function() {
    // return response(\App\Models\User::where('email', 'julistian@gmail.com')->withCount('chats')->first());
    $users = \App\Models\User::where('email', 'wilsen@gmail.com')->first()->chats->count();
    return response($users);
});