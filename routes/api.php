
<?php

use App\Http\Controllers\Auth\CitizenAuthController;
use App\Http\Controllers\ComplaintController;
<<<<<<< HEAD
use App\Http\Controllers\government_agencie;
=======
>>>>>>> 1718eb7ba15695ab7a4044b614f739c7b2f46d69
use Illuminate\Http\Request;
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
Route::post('/citizen/register', [CitizenAuthController::class, 'register']);
<<<<<<< HEAD
Route::post('/citizen/verify-email/{user_id}', [CitizenAuthController::class, 'verifyEmail']);
Route::post('login', [CitizenAuthController::class, 'login'])->
middleware('role.throttle');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/complaints', [ComplaintController::class, 'store']);
    Route::get('/index', [government_agencie::class, 'index']);
    Route::post('logout',[CitizenAuthController::class,'logout']);
    Route::post('add_employee', [\App\Http\Controllers\EmployeeController::class,
        'add_employee'])->name('add_employee')->middleware('can:add_employee');
=======

// مسار التحقق: يستقبل user_id كمتغير في المسار، و code في جسم الطلب
// تم التعديل هنا: /citizen/verify-email/{user_id}
Route::post('/citizen/verify-email/{user_id}', [CitizenAuthController::class, 'verifyEmail']);


Route::middleware('auth:sanctum')->group(function () {

    // مسار لتقديم الشكوى
    Route::post('/complaints', [ComplaintController::class, 'store']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });


>>>>>>> 1718eb7ba15695ab7a4044b614f739c7b2f46d69
});

