<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
	dd(phpinfo());
    return ['Laravel' => app()->version()];
});

require __DIR__.'/auth.php';

//Command Route

Route::get('command/config-cache', function () {
    \Artisan::call('config:cache');

    dd("cache run");
});
Route::get('command/config-clear', function () {
    \Artisan::call('config:clear');

    dd("config cleared");
});
Route::get('command/cache-clear', function () {
    \Artisan::call('cache:clear');

    dd("cache run");
});

Route::get('command/migrate', function () {
    \Artisan::call('migrate');

    dd("migrated successfully");
});
Route::get('command/storage-link', function () {
    \Artisan::call('storage:link');

    dd("Storage Link Created successfully");
});
Route::get('command/permissions-clear', function () {
	// Reset cached roles and permissions
	app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
	
	dd("Cached roles and permissions reset");
});
