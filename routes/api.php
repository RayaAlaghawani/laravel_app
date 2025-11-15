
<?php

use App\Http\Controllers\Auth\CitizenAuthController;
use App\Http\Controllers\ComplaintController;
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

// مسار التحقق: يستقبل user_id كمتغير في المسار، و code في جسم الطلب
// تم التعديل هنا: /citizen/verify-email/{user_id}
Route::post('/citizen/verify-email/{user_id}', [CitizenAuthController::class, 'verifyEmail']);


Route::middleware('auth:sanctum')->group(function () {

    // مسار لتقديم الشكوى
    Route::post('/complaints', [ComplaintController::class, 'store']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });


});





Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
