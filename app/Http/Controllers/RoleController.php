<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\CreateRequest;
use App\Http\Requests\Role\UpdateRequest;
use App\Http\Requests\Role\DeleteRequest;
use Illuminate\Support\Facades\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use App\Http\Resources\RoleResource;
use Log;

class RoleController extends Controller
{
	/**
     * Fetch saved user roles.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
		$roles = Role::orderBy('name', 'ASC')->get();
        return RoleResource::collection($roles);
    }
	
    /**
     * Handle an incoming create role request.
     *
     * @param  \App\Http\Requests\Role\CreateRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateRequest $request)
    {
		$role = Role::create(['name' => $request->name]);
		
		// Reset cached roles and permissions
		app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
		
		$role = (new RoleResource($role))->toArray($request);
		return Response::json(['role' => $role, 'message' => "Role created successfully."], 200);
    }
	
	/**
     * Handle an incoming update role request.
     *
     * @param  \App\Http\Requests\Role\UpdateRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request)
    {
		$role = Role::find($request->id);
		$role->syncPermissions($request->permissions);
		
		// Reset cached roles and permissions
		app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
		
		$role = (new RoleResource($role))->toArray($request);
		return Response::json(['role' => $role, 'message' => "Role updated successfully."], 200);
    }
	
	/**
     * Handle an incoming delete role request.
     *
     * @param  \App\Http\Requests\Role\DeleteRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(DeleteRequest $request)
    {
		Role::find($request->role_id)->delete;
		
		// Reset cached roles and permissions
		app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
		
		return Response::json(['message' => "Role deleted successfully."], 200);
    }
    
}
