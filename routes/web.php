<?php

use App\Http\Controllers\ActivitiesLogController;
use App\Http\Controllers\user\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {

    return $request->user();

});



Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/updateProfile/{user}', [UserController::class, 'update']);
    Route::get('/getUser/{user}', [UserController::class, 'getUser']);
    Route::patch('/updatePwd/{user}', [UserController::class, 'updatePwd']);
    Route::get('/fetchUsers', [UserController::class, 'fetchUsers'])->middleware('isAdmin');
    Route::get('/download', [UserController::class, 'download'])->middleware('isAdmin');
    Route::post('/assignKey/{user}', [UserController::class, 'assignPrivateKey'])->middleware('isAdmin');
    Route::get('/getKey/{user}', [UserController::class, 'getKey']);
    Route::post('/changeLock/{user}', [UserController::class, 'toogleLock']);
    Route::post('/uploadKey/{user}', [UserController::class, 'uploadPriKey']);
    Route::get('/fetchLogs/{user}', [ActivitiesLogController::class, 'fetchLogs'])->middleware('isAdmin');
    
});

Route::get('/', function () {

    return ['Laravel' => app()->version()];

});

require __DIR__.'/auth.php';
