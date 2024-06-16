<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */

Route::prefix('employee')->group(function () {
    Route::post('create', [EmployeeController::class, 'create']);
});

Route::prefix('transaction')->group(function() {
    Route::post('create', [TransactionController::class, 'create']);
    Route::get('getunpaidemployees', [TransactionController::class, 'getUnpaidEmployees']);
    Route::get('commitall', [TransactionController::class, 'commitAll']);
});