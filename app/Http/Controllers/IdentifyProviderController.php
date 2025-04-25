<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class IdentifyProviderController extends Controller
{
    protected $shopifyClientId;
    protected $shopifyClientSecret;
    protected $shopifyRedirectUri;
    protected $shopifyScopes;

    public function __construct()
    {
        $this->shopifyClientId = env('SHOPIFY_CLIENT_ID');
        $this->shopifyClientSecret = env('SHOPIFY_CLIENT_SECRET');
        $this->shopifyRedirectUri = env('SHOPIFY_REDIRECT_URI');
        $this->shopifyScopes = env('SHOPIFY_SCOPES', 'read_products,write_products');
    }

    /**
     * Handle the identify provider request from Shopify
     */
    public function identify(Request $request)
    {
        // Validate the request
        $request->validate([
            'shop' => 'required|string',
            'email' => 'required|email',
            'hmac' => 'required|string',
            'timestamp' => 'required|string',
            'return_to' => 'required|url',
        ]);

        // Verify HMAC signature
        if (!$this->verifyHmac($request)) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        // Check if the timestamp is within 5 minutes
        $timestamp = (int) $request->timestamp;
        if (abs(time() - $timestamp) > 300) {
            return response()->json(['error' => 'Request expired'], 403);
        }

        // Find user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Generate a one-time token for this specific login
        $token = $this->generateOneTimeToken($user, $request->shop);

        // Redirect to Shopify with the token
        $redirectUrl = $request->return_to . '?' . http_build_query([
            'token' => $token,
            'email' => $user->email,
        ]);

        return redirect($redirectUrl);
    }

    /**
     * Verify the HMAC signature from Shopify
     */
    protected function verifyHmac(Request $request)
    {
        $params = $request->except('hmac');
        ksort($params);
        
        $computedHmac = hash_hmac(
            'sha256',
            http_build_query($params),
            $this->shopifyClientSecret
        );

        return hash_equals($computedHmac, $request->hmac);
    }

    /**
     * Generate a one-time token for the user
     */
    protected function generateOneTimeToken(User $user, string $shop)
    {
        // Create a token that expires in 5 minutes
        $payload = [
            'user_id' => $user->id,
            'email' => $user->email,
            'shop' => $shop,
            'exp' => time() + 300, // 5 minutes
        ];

        // Sign the token with the client secret
        return base64_encode(json_encode($payload)) . '.' . 
               hash_hmac('sha256', json_encode($payload), $this->shopifyClientSecret);
    }

    /**
     * Verify and process the one-time token
     */
    public function verifyToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
        ]);

        try {
            // Split the token
            list($payload, $signature) = explode('.', $request->token);
            
            // Verify the signature
            $expectedSignature = hash_hmac('sha256', $payload, $this->shopifyClientSecret);
            if (!hash_equals($expectedSignature, $signature)) {
                return response()->json(['error' => 'Invalid token'], 403);
            }

            // Decode the payload
            $payload = json_decode(base64_decode($payload), true);
            
            // Check if the token has expired
            if ($payload['exp'] < time()) {
                return response()->json(['error' => 'Token expired'], 403);
            }

            // Check if the email matches
            if ($payload['email'] !== $request->email) {
                return response()->json(['error' => 'Email mismatch'], 403);
            }

            // Find the user
            $user = User::find($payload['user_id']);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            // Generate a Shopify access token for this user
            $shopifyToken = $this->generateShopifyAccessToken($user, $payload['shop']);

            return response()->json([
                'access_token' => $shopifyToken,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Token verification error', [
                'error' => $e->getMessage(),
                'token' => $request->token
            ]);
            
            return response()->json(['error' => 'Invalid token format'], 400);
        }
    }

    /**
     * Generate a Shopify access token for the user
     */
    protected function generateShopifyAccessToken(User $user, string $shop)
    {
        // This is a simplified version. In a real implementation, you would:
        // 1. Check if the user already has a valid token for this shop
        // 2. If not, use the OAuth flow to get a new token
        // 3. Store the token securely
        
        // For this example, we'll just generate a temporary token
        return hash_hmac('sha256', $user->id . $shop . time(), $this->shopifyClientSecret);
    }
} 