<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SubscriptionController;
use App\Lib\LemonSqueezy\LemonSqueezy;
use App\Lib\LemonSqueezy\Controllers\WebhookController;
use Spatie\Permission\Models\Role;

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

Route::get('/pricing', [SubscriptionController::class, 'index']);
Route::get('/templates', [TemplateController::class, 'index']);
Route::get('/templates/{uuid}', [TemplateController::class, 'load']);
Route::post('/users/register', [RegisteredUserController::class, 'store']);
Route::post('/users/login', [AuthenticatedSessionController::class, 'store']);

//generate pdf
Route::post('/create-pdf/{id}', [DocumentController::class, 'createPdf']);
	
Route::middleware('auth:sanctum')->group(function () {

	//stripe
	Route::post('/stripe/subscribe', [SubscriptionController::class, 'subscribe']);
	Route::post('/stripe/cancel', [SubscriptionController::class, 'cancel']);
	Route::post('/stripe/resume', [SubscriptionController::class, 'resume']);
	Route::post('/stripe/payment-methods', [PaymentController::class, 'storePaymentMethod']);
	
	Route::get('/subscription-plans', [SubscriptionController::class, 'index']);
	Route::get('/subscription-plans/{uuid}', [SubscriptionController::class, 'view']);

	Route::get('/documents', [DocumentController::class, 'index']);
	Route::get('/api-key', [UserController::class, 'apiKey']);
	Route::get('/stripe/payment-methods', [UserController::class, 'getPaymentMethods']);
	Route::get('/current-subscription', [UserController::class, 'getCurrentSubscriptionDetails']);
	Route::post('/api-key', [UserController::class, 'refreshApiKey']);
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
	//roles & permissions
	Route::get('/admin/roles', [RoleController::class, 'index']);
	Route::post('/admin/roles/create', [RoleController::class, 'store']);
	Route::post('/admin/roles/{id}/update', [RoleController::class, 'update']);
	Route::post('/admin/roles/{id}/rename', [RoleController::class, 'rename']);
	Route::post('/admin/roles/{id}/delete', [RoleController::class, 'delete']);
	//users
	Route::get('/admin/users', [UserController::class, 'index']);
	Route::post('/admin/users/{id}/update/roles', [UserController::class, 'updateRoles']);
    //subscriptions
	Route::get('/admin/subscriptions', [SubscriptionController::class, 'index']);
    Route::post('/admin/subscriptions', [SubscriptionController::class, 'store']);
    Route::get('/admin/subscriptions/{uuid}', [SubscriptionController::class, 'view']);
	Route::post('/admin/subscriptions/{uuid}', [SubscriptionController::class, 'update']);
	Route::post('/admin/subscriptions/{uuid}/delete', [SubscriptionController::class, 'delete']);
    Route::post('/admin/subscriptions/{uuid}/restore', [SubscriptionController::class, 'restore']);
	Route::post('/admin/subscriptions/{uuid}/destroy', [SubscriptionController::class, 'destroy']);
	
});
#lemon squeezy
Route::post('/lemonsqueezy/webhook', WebhookController::class);
Route::get('/stores', function (Request $request) {
    $stores = LemonSqueezy::stores();
    print($stores);
});

Route::get('/customers/create', function (Request $request) {
    $stores = LemonSqueezy::createCustomer();
    print($stores);
});

Route::get('/subscriptions', function (Request $request) {
    $stores = LemonSqueezy::variants();
    print($stores);
});
Route::get('/r', function (Request $request) {
	Role::create(['name' => 'user', 'guard_name' => 'web']);
	Role::create(['name' => 'admin', 'guard_name' => 'web']);
});
