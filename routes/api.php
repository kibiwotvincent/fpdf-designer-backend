<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;

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

//Route::get('/admin/roles', [RoleController::class, 'index']);
Route::get('/settings/init', [SettingController::class, 'init']);
Route::get('/workspace/init/blank/{saveAs?}', [WorkspaceController::class, 'initBlank']);
Route::get('/workspace/init/{source}/{uuid}/{saveAs?}', [WorkspaceController::class, 'initFromSource']);
Route::get('/workspace/{uuid}', [WorkspaceController::class, 'load']);
Route::post('/workspace/{uuid}/reset', [WorkspaceController::class, 'reset']);
Route::get('/workspace/{uuid}/preview', [WorkspaceController::class, 'preview']);
Route::post('/workspace/{uuid}/upload-image', [WorkspaceController::class, 'uploadImage']);

Route::get('/templates', [TemplateController::class, 'index']);
Route::get('/templates/{uuid}', [TemplateController::class, 'load']);
Route::post('/users/register', [RegisteredUserController::class, 'store']);
Route::post('/users/login', [AuthenticatedSessionController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
	Route::get('/documents', [DocumentController::class, 'index']);
	Route::post('/documents/{uuid}/rename', [DocumentController::class, 'renameDocument']);
	Route::get('/documents/{uuid}/view-pdf', [DocumentController::class, 'viewPdf']);
	Route::post('/documents/{uuid}/delete', [DocumentController::class, 'delete']);
	Route::post('/workspace/{uuid}/save', [WorkspaceController::class, 'save']);
	
	//admin routes
	//templates
	Route::get('/admin/templates', [TemplateController::class, 'index']);
	Route::post('/admin/templates/{uuid}/rename', [TemplateController::class, 'renameTemplate']);
	Route::get('/admin/templates/{uuid}/view-pdf', [TemplateController::class, 'viewPdf']);
	Route::post('/admin/templates/{uuid}/delete', [TemplateController::class, 'delete']);
	//roles
	Route::get('/admin/permissions', [PermissionController::class, 'index']);
	Route::get('/admin/roles', [RoleController::class, 'index']);
	Route::post('/admin/roles/create', [RoleController::class, 'store']);
	Route::post('/admin/roles/{id}/update', [RoleController::class, 'update']);
	Route::post('/admin/roles/{id}/rename', [RoleController::class, 'rename']);
	Route::post('/admin/roles/{id}/delete', [RoleController::class, 'delete']);
});
