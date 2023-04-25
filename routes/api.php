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

Route::get('/page/setup', function (Request $request) {
	$fonts = ['Arial','Calibri','Helvetica','Times'];
	$pageSizes = [
					'A4' => ['width' => 210,'height' => 297],
					'A6' => ['width' => 148.5,'height' => 210]
					];
	$pageMargins = [
					'none' => ['top_margin' => 0,'right_margin' => 0,'bottom_margin' => 0,'left_margin' => 0],
					'small' => ['top_margin' => 5,'right_margin' => 5,'bottom_margin' => 5,'left_margin' => 5],
					'medium' => ['top_margin' => 10,'right_margin' => 10,'bottom_margin' => 10,'left_margin' => 10],
					'custom' => ['top_margin' => 10,'right_margin' => 10,'bottom_margin' => 10,'left_margin' => 10],
					];
    return response()->json(['page_sizes' => $pageSizes, 'page_margins' => $pageMargins, 'fonts' => $fonts]);
});

Route::get('/templates', [DocumentController::class, 'index']);
Route::get('/templates/{id}', [DocumentController::class, 'load']);

Route::post('/users/register', [RegisteredUserController::class, 'store']);
Route::post('/users/login', [AuthenticatedSessionController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
	Route::get('/documents', [DocumentController::class, 'index']);
	Route::post('/documents/save', [DocumentController::class, 'store']);
	Route::get('/documents/{id}', [DocumentController::class, 'load']);
	Route::post('/documents/update', [DocumentController::class, 'update']);
	//Route::get('/documents/{id}/preview', [DocumentController::class, 'preview']);
});

Route::get('/documents/{id}/preview', [DocumentController::class, 'preview']);