<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permission\CreateRequest;
use App\Http\Requests\Permission\UpdateRequest;
use Illuminate\Support\Facades\Response;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Resources\PermissionResource;

class PermissionController extends Controller
{
	/**
     * Fetch available permissions.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() 
	{
		$permissions = Permission::orderBy('name', 'ASC')->get();
        return PermissionResource::collection($permissions);
    }
	
    /**
     * Handle an incoming create permission request.
     *
     * @param  \App\Http\Requests\Permission\CreateRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateRequest $request)
    {
		$permission = Permission::create(['name' => $request->name, 'guard_name' => 'web']);
		
		// Reset cached permissions and permissions
		app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
		
		$permission = (new PermissionResource($permission))->toArray($request);
		return Response::json(['permission' => $permission, 'message' => "Permission added successfully."], 200);
    }
	
	/**
     * Handle an incoming update permission request.
     *
     * @param  \App\Http\Requests\Permission\UpdateRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request)
    {
		$permission = Permission::find($request->id);
		$permission->name = $request->name;
		$permission->save();
		
		// Reset cached permissions and permissions
		app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
		
		$permission = (new PermissionResource($permission))->toArray($request);
		return Response::json(['permission' => $permission, 'message' => "Permission updated successfully."], 200);
    }
	
	/**
     * Handle an incoming delete permission request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
		Permission::find($request->id)->delete();
		
		// Reset cached permissions and permissions
		app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
		
		return Response::json(['message' => "Permission deleted successfully."], 200);
    }
    
}
