<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\User\UpdateRolesRequest;

class UserController extends Controller
{
	/**
     * Fetch users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $users = User::orderBy('name', 'asc')->get();
		return UserResource::collection($users);
    }
	
	/**
     * Handle an incoming update user roles request.
     *
     * @param  \App\Http\Requests\User\UpdateRolesRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateRoles(UpdateRolesRequest $request)
    {
		$user = User::find($request->id);
		
		//delete current user roles
		DB::delete("DELETE FROM model_has_roles WHERE model_type = :model_type AND model_id = :model_id", ['model_type' => "App\Models\User", 'model_id' => $user->id]);
		
		//assign user roles as selected
		foreach($request->roles as $role) {
			$user->assignRole($role);
		}
		
		// Reset cached roles and permissions
		app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
		
		$user = (new UserResource($user))->toArray($request);
		return Response::json(['user' => $user, 'message' => "User roles updated successfully."], 200);
    }
}
