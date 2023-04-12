<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DocumentController;

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
					['type' => 'image', 'left' => 0, 'top' => 0, 'width' => 180, 'height' => 120, 
					'url' => "http://localhost:8000/logo.png"],
					['type' => 'text', 'left' => 600, 'top' => 10, 'width' => 116, 'height' => 50, 'text' => "INVOICE", 
					'font_weight' => 'bold','font_size' => 18,'background_color' => '#fffffffff', 'padding_right' => '0',
					'text_align' => 'right'],
					['type' => 'text','left' => 0, 'top' => 130, 'width' => 340, 'height' => 35, 'text' => "OUR INFORMATION", 
					'font_weight' => 'bold','font_color' => '#2E3E4E','padding_bottom' => '15', 
					'border_bottom' => '1', 'border_color' => '#2E3E4E', 'border_weight' => '3'],
					['type' => 'text','left' => 376, 'top' => 130, 'width' => 340, 'height' => 35, 'text' => "BILLING TO", 
					'font_weight' => 'bold','font_color' => '#2E3E4E','padding_bottom' => '15', 
					'border_bottom' => '1', 'border_color' => '#2E3E4E', 'border_weight' => '3'],
					
					['type' => 'text','left' => 0, 'top' => 170, 'width' => 340, 'height' => 50, 'text' => "Scribes Insurance Agency", 
					'font_weight' => 'bold','font_size' => 12,'background_color' => '#ffffff', 'padding_right' => '0'],
					['type' => 'text','left' => 0, 'top' => 220, 'width' => 340, 'height' => 20, 'text' => "40 E 7th St, New York, NY 10003, USA, New York", 
					'font_weight' => 'normal','font_size' => 11, 'background_color' => '#ffffff', 'padding_right' => '0'],
					['type' => 'text','left' => 0, 'top' => 240, 'width' => 340, 'height' => 20, 'text' => "New York, 62070,", 
					'font_weight' => 'normal','font_size' => 11,'background_color' => '#ffffff', 'padding_right' => '0'],
					['type' => 'text','left' => 0, 'top' => 260, 'width' => 340, 'height' => 20, 'text' => "United States", 
					'font_weight' => 'normal','font_size' => 11,'background_color' => '#ffffff', 'padding_right' => '0'],
					['type' => 'text','left' => 0, 'top' => 280, 'width' => 340, 'height' => 20, 'text' => "Phone: 254726397671", 
					'font_weight' => 'normal','font_size' => 11,'background_color' => '#ffffff', 'padding_right' => '0'],
					['type' => 'text','left' => 0, 'top' => 300, 'width' => 340, 'height' => 20, 'text' => "Email: info@elantsys.com.", 
					'font_weight' => 'normal','font_size' => 11,'background_color' => '#ffffff', 'padding_right' => '0'],
					
					['type' => 'text','left' => 376, 'top' => 170, 'width' => 340, 'height' => 50, 'text' => "JOSÉ BERNARDO LIMA", 
					'font_weight' => 'bold','font_size' => 12,'background_color' => '#ffffff', 'padding_right' => '0'],
					['type' => 'text','left' => 376, 'top' => 220, 'width' => 340, 'height' => 20, 'text' => "Avenida das Flores, 149, São Paulo", 
					'font_weight' => 'normal','font_size' => 11, 'background_color' => '#ffffff', 'padding_right' => '0'],
					['type' => 'text','left' => 376, 'top' => 240, 'width' => 340, 'height' => 20, 'text' => "Guarulhos, 07295-043,", 
					'font_weight' => 'normal','font_size' => 11,'background_color' => '#ffffff', 'padding_right' => '0'],
					['type' => 'text','left' => 376, 'top' => 260, 'width' => 340, 'height' => 20, 'text' => "Brasil", 
					'font_weight' => 'normal','font_size' => 11,'background_color' => '#ffffff', 'padding_right' => '0'],
					['type' => 'text','left' => 376, 'top' => 280, 'width' => 340, 'height' => 20, 'text' => "Phone: (11) 2432-2590", 
					'font_weight' => 'normal','font_size' => 11,'background_color' => '#ffffff', 'padding_right' => '0'],
					['type' => 'text','left' => 376, 'top' => 300, 'width' => 340, 'height' => 20, 'text' => "Email: jose.lima@gmail.com.", 
					'font_weight' => 'normal','font_size' => 11,'background_color' => '#ffffff', 'padding_right' => '0'],
					
					['type' => 'text','left' => 0, 'top' => 350, 'width' => 362, 'height' => 40, 'text' => "Product", 
					'font_weight' => 'bold', 'font_color' => '#ffffff','text_align' => 'center', 'background_color' => '#2e3e4e'],
					['type' => 'text','left' => 360, 'top' => 350, 'width' => 82, 'height' => 40, 'text' => "Quantity", 
					'font_weight' => 'bold','font_color' => '#ffffff','text_align' => 'center','background_color' => '#2e3e4e'],
					['type' => 'text','left' => 440, 'top' => 350, 'width' => 102, 'height' => 40, 'text' => "Price", 
					'font_weight' => 'bold','font_color' => '#ffffff','text_align' => 'right','background_color' => '#2e3e4e','padding_right' => '5'],
					['type' => 'text','left' => 540, 'top' => 350, 'width' => 62, 'height' => 40, 'text' => "Tax", 
					'font_weight' => 'bold','font_color' => '#ffffff','text_align' => 'center','background_color' => '#2e3e4e'],
					['type' => 'text','left' => 600, 'top' => 350, 'width' => 116, 'height' => 40, 'text' => "Total", 
					'font_weight' => 'bold','font_color' => '#ffffff','text_align' => 'right','background_color' => '#2e3e4e','padding_right' => '5'],
					
					['type' => 'text','left' => 0, 'top' => 392, 'width' => 360, 'height' => 40, 'text' => "Computer", 
					'font_weight' => 'bold', 'font_color' => '#000000','text_align' => 'left', 'background_color' => '#eeeeee', 'padding_left' => '5'],
					['type' => 'text','left' => 360, 'top' => 392, 'width' => 80, 'height' => 40, 'text' => "1.00", 
					'font_weight' => 'normal','font_color' => '#000000','text_align' => 'center','background_color' => '#eeeeee'],
					['type' => 'text','left' => 440, 'top' => 392, 'width' => 100, 'height' => 40, 'text' => "2,800.00", 
					'font_weight' => 'normal','font_color' => '#000000','text_align' => 'right','background_color' => '#eeeeee','padding_right' => '5'],
					['type' => 'text','left' => 540, 'top' => 392, 'width' => 60, 'height' => 40, 'text' => "7%", 
					'font_weight' => 'normal','font_color' => '#000000','text_align' => 'center','background_color' => '#eeeeee'],
					['type' => 'text','left' => 600, 'top' => 392, 'width' => 116, 'height' => 40, 'text' => "3,000.00", 
					'font_weight' => 'normal','font_color' => '#000000','text_align' => 'right','background_color' => '#eeeeee','padding_right' => '5'],
					
				 ];
    return response()->json(['draggables' => $draggables]);
});

Route::post('/users/register', [RegisteredUserController::class, 'store']);
Route::post('/users/login', [AuthenticatedSessionController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
	Route::get('/documents', [DocumentController::class, 'index']);
	Route::post('/documents/save', [DocumentController::class, 'store']);
	Route::get('/documents/{id}', [DocumentController::class, 'load']);
	Route::post('/documents/update', [DocumentController::class, 'update']);
});
