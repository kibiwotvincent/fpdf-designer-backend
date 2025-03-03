<?php

namespace App\Http\Requests;

use App\Events\CreatePdfRequestReceived;
use App\Exceptions\InactiveSubscriptionException;
use App\Exceptions\InvalidApiKeyException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class CreatePdfRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //'id' => 'required',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws App\Exceptions\InvalidLoginException
     */
    public function authenticate()
    {
        $this->ensureIsNotRateLimited();

        $user = User::where('api_key', $this->bearerToken())->first();
        if ($user === null) {
            RateLimiter::hit($this->throttleKey());

            throw new InvalidApiKeyException("Invalid API Key. Try again!");
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Check if user has active subscription.
     *
     * @return void
     *
     * @throws App\Exceptions\InactiveSubscriptionException
     */
    public function checkForActiveSubscription()
    {
        $user = User::where('api_key', $this->bearerToken())->first();
        if (! $user->isSubscribed()) {
            $reason = $user->notSubscribedReason();
            throw new InactiveSubscriptionException($reason);
        }
    }

    /**
     * Ensure the create pdf request is not rate limited.
     *
     * @return void
     *
     * @throws App\Exceptions\InvalidLoginException
     */
    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 30)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

		throw new InvalidApiKeyException(trans('auth.throttle', [
					'seconds' => $seconds,
					'minutes' => ceil($seconds / 60),
				]));
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->bearerToken()).'|'.$this->ip();
    }
	
	/**
     * Dispatch event that will log the request in database.
     *
     * @return bool
     */
    public function report()
    {
        $requestPayload = [
            'api_key' => $this->bearerToken(),
            'document_id' => $this->id,
            'ip_address' => $this->ip()
       ];

       \Log::debug($this);

       CreatePdfRequestReceived::dispatch($requestPayload);
    }
}
