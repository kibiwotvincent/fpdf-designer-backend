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
     * Fetch user api key.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiKey(Request $request)
    {
        $user = User::find($request->user()->id);
		return Response::json(['api_key' => $user->api_key, 'message' => "Api key fetched successfully."], 200);
    }
	
    /**
     * Regenerate user api key.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshApiKey(Request $request)
    {
        $user = User::find($request->user()->id);
        $user->generateApiKey();
        $user->refresh();
		return Response::json(['api_key' => $user->api_key, 'message' => "New API key has been generated successfully."], 200);
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

    public function getPaymentMethods(Request $request)
    {
        $user = $request->user();
        $paymentMethods = [];
        try {
          foreach($user->paymentMethods() as $paymentMethod) {
            array_push($paymentMethods, $paymentMethod);
          }
        } catch (\Exception $e) {
            return response()->json(['error' => "Network error. Could not connect to stripe."], 400);
        }

        return response()->json([
            'payment_methods' => $paymentMethods,
            'default_payment_method' => $user->defaultPaymentMethod()
        ]);
    }

    public function getCurrentSubscriptionDetails(Request $request)
    {
        $user = $request->user();
        $userSubscribedPlan = $user->getSubscribedPlan();

        if ($user->isOnFreePlan()) {
            $isFreePlan = true;
            $isActive = true;
            $validUntil = false;
            $onGracePeriod = false;
            $isCancelled = false;
        } else {
            $subscription = $user->subscription('default'); // Get active subscription
            
            $isFreePlan = false;
            $isActive = true;
            $validUntil = \Carbon\Carbon::createFromTimestamp($subscription->asStripeSubscription()->current_period_end)->format('F j, Y');
            $onGracePeriod = $user->subscription('default')->onGracePeriod();
            $isCancelled = $user->subscription('default')->cancelled();
        }

        return response()->json([
            'id' => $userSubscribedPlan->uuid,
            'title' => $userSubscribedPlan->title,
            'is_active' => $isActive,
            'is_free_plan' => $isFreePlan,
            'valid_until' => $validUntil,
            'is_on_grace_period' => $onGracePeriod,
            'is_cancelled' => $isCancelled
        ]);
    }
}
