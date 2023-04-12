<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/templates', function (Request $request) {
	//sleep(3);
	$templates = [
					['id'=>'1','url'=>"http://localhost:8080/1.PNG"],
					['id'=>'2','url'=>"http://localhost:8080/1.PNG"],
					['id'=>'3','url'=>"http://localhost:8080/1.PNG"],
					['id'=>'4','url'=>"http://localhost:8080/1.PNG"],
				 ];
    return response()->json($templates);
});
Route::get('/templates/{id}', function (Request $request) {
	$draggables = [
					['id' => 1, 'left' => 0, 'top' => 20, 'width' => 716, 'height' => 50, 'text' => "STATUS: PAID", 
					'font_weight' => 'bold','background_color' => '#bae6fd', 'padding_right' => '10'],
					['id' => 2, 'left' => 0, 'top' => 150, 'width' => 716, 'height' => 30, 'text' => "Scribes Insurance Agency", 
					'font_weight' => 'bold','background_color' => '#fff', 'padding_right' => '0'],
					['id' => 3, 'left' => 0, 'top' => 180, 'width' => 716, 'height' => 24, 'text' => "P.O Box 3478-4302", 
					'font_weight' => 'normal','background_color' => '#fff', 'padding_right' => '0'],
					['id' => 4, 'left' => 0, 'top' => 204, 'width' => 716, 'height' => 24, 'text' => "Olkalou", 
					'font_weight' => 'normal','background_color' => '#fff', 'padding_right' => '0'],
					['id' => 5, 'left' => 0, 'top' => 228, 'width' => 716, 'height' => 24, 'text' => "Phone: 07040283732", 
					'font_weight' => 'normal','background_color' => '#fff', 'padding_right' => '0'],
					
					['id' => 6, 'left' => 0, 'top' => 328, 'width' => 200, 'height' => 24, 'text' => "Product", 
					'font_weight' => 'normal','background_color' => '#2e3e4e', 'padding_right' => '0'],
					['id' => 7, 'left' => 200, 'top' => 328, 'width' => 116, 'height' => 24, 'text' => "Quantity", 
					'font_weight' => 'normal','background_color' => '#fff', 'padding_right' => '0'],
					['id' => 8, 'left' => 316, 'top' => 328, 'width' => 200, 'height' => 24, 'text' => "Price", 
					'font_weight' => 'normal','background_color' => '#fff', 'padding_right' => '0'],
					['id' => 9, 'left' => 516, 'top' => 328, 'width' => 200, 'height' => 24, 'text' => "Total", 
					'font_weight' => 'normal','background_color' => '#fff', 'padding_right' => '0'],
					
				 ];
    return response()->json(['draggables' => $draggables]);
});

Route::post('/users/register', [RegisteredUserController::class, 'store']);
Route::post('/users/login', [AuthenticatedSessionController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {

});
