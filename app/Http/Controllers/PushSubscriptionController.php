<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PushSubscriptionController extends Controller
{
    /**
     * Store the Push Subscription.
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'endpoint' => 'required',
                'keys.auth' => 'required',
                'keys.p256dh' => 'required',
            ]);

            $endpoint = $request->endpoint;
            $token = $request->keys['auth'];
            $key = $request->keys['p256dh'];

            $user = Auth::user();

            // Update or create the subscription for the user
            $user->updatePushSubscription($endpoint, $key, $token);

            return response()->json(['success' => true], 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Push Subscription Error: '.$e->getMessage());

            return response()->json(['error' => 'Subscription failed. Please try again later.'], 500);
        }
    }
}
