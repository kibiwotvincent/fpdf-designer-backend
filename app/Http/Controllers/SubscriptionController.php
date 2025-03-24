<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscribeRequest;
use Illuminate\Http\Request;
use App\Http\Requests\Subscription\CreateRequest;
use App\Http\Requests\Subscription\UpdateRequest;
use App\Http\Resources\SubscriptionResource;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Str;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $bearerToken = request()->bearerToken();

        if($bearerToken != null) {
            //use sanctum middleware on index only
            $this->middleware('auth:sanctum')->only('index');
        }
    }

    /**
     * Fetch available subscriptions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        //$this->authorize('viewAny', Subscription::class);
        
        if($request->all == true) {
            $subscriptions = Subscription::withTrashed()->orderBy('price', 'asc')->get();
        }
        else {
            $subscriptions = Subscription::orderBy('price', 'asc')->get();
        }
        
		return SubscriptionResource::collection($subscriptions);
    }
    
    /**
     * Fetch subscription info.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function view(Request $request)
    {
        $subscription = Subscription::where('uuid', $request->uuid)->withTrashed()->first();
        //$this->authorize('view', $subscription);
        
		return new SubscriptionResource($subscription);
    }
    
    /**
     * Fetch available subscriptions.
     *
     * @param  App\Http\Requests\Subscription\CreateRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateRequest $request)
    {
        $this->authorize('create', Subscription::class);
        $data = $request->validated();
        $subscription = Subscription::create([
										'uuid' => Str::uuid(),
										'title' => $data['title'],
										'price' => $data['price'],
										'description' => $data['description'],
                                        'items' => $data['items'],
                                        'duration' => $data['duration'],
                                        'duration_type' => $data['duration_type'],
                                        'stripe_name' => $data['stripe_name'],
                                        'stripe_price_id' => $data['stripe_price_id'],
										]);
        
		$subscription = (new SubscriptionResource($subscription))->toArray($request);
		return Response::json(['subscription' => $subscription, 'message' => "Subscription added successfully."], 200);
    }
    
    /**
     * Fetch available subscriptions.
     *
     * @param  App\Http\Requests\Subscription\UpdateRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request)
    {
        $subscription = Subscription::where('uuid', $request->uuid)->withTrashed()->first();
        $this->authorize('update', $subscription);
        $data = $request->validated();
        
        $subscription->title = $data['title'];
        $subscription->price = $data['price'];
        $subscription->description = $data['description'];
        $subscription->items = $data['items'];
        $subscription->duration = $data['duration'];
        $subscription->duration_type = $data['duration_type'];
        $subscription->stripe_name = $data['stripe_name'];
        $subscription->stripe_price_id = $data['stripe_price_id'];
        $subscription->save();
        
		$subscription = (new SubscriptionResource($subscription))->toArray($request);
		return Response::json(['subscription' => $subscription, 'message' => "Subscription updated successfully."], 200);
    }
    
    /**
     * Handle an incoming delete subscription request.
     *
     * @param  @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {	
		$subscription = Subscription::where('uuid', $request->uuid)->first();
        $this->authorize('delete', $subscription);
        $subscription->delete();
		return response()->json(['message' => "Subscription has been deleted successfully."], 200);
	}
    
    /**
     * Handle an incoming restore deleted subscription request.
     *
     * @param  @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore(Request $request)
    {	
		$subscription = Subscription::where('uuid', $request->uuid)->withTrashed()->first();
        $this->authorize('restore', $subscription);
        $subscription->restore();
		return response()->json(['message' => "Subscription has been restored successfully."], 200);
	}
    
    /**
     * Handle an incoming destroy subscription request.
     *
     * @param  @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {	
		$subscription = Subscription::where('uuid', $request->uuid)->withTrashed()->first();
        $this->authorize('forceDelete', $subscription);
        $subscription->forceDelete();
		return response()->json(['message' => "Subscription has been permanently deleted successfully."], 200);
	}

    public function subscribe(SubscribeRequest $request) 
    {
        $user = $request->user();
        $priceId = $request->price_id;
        $paymentMethodId = $request->payment_method;

        // Update the subscription payment method
        $user->updateDefaultPaymentMethod($paymentMethodId);


        if ($user->isOnFreePlan()) {
            //create new subscription
            $user->newSubscription('default', $priceId)->create();
        } else {
            // Swap to the new plan with proration
            $user->subscription('default')->swap($priceId);
        }
    
        return response()->json(['message' => "Payment successful. Subscription has been activated."], 200);
    }

    public function cancel(Request $request) 
    {
        $user = $request->user();

        if (! $user->isOnFreePlan() && !$user->subscription('default')->onGracePeriod()) {
            $user->subscription('default')->cancel();
        }
    
        return response()->json(['message' => "Subscription has been set to cancel but you still have access until the end of the billing period."], 200);
    }

    public function resume(Request $request) 
    {
        $user = $request->user();

        if (! $user->isOnFreePlan() && $user->subscription('default')->onGracePeriod()) {
            $user->subscription('default')->resume();
        }
    
        return response()->json(['message' => "Subscription has been resumed."], 200);
    }
}
