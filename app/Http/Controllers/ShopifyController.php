<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class ShopifyController extends Controller
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
     * Redirect to Shopify authorization page
     */
    public function redirect()
    {
        $shop = request('shop');
        
        if (!$shop) {
            return redirect()->route('home')->with('error', 'Shop parameter is required');
        }

        // Validate shop domain
        if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\-]*\.myshopify\.com$/', $shop)) {
            return redirect()->route('home')->with('error', 'Invalid shop domain');
        }

        // Generate a random state parameter for security
        $state = bin2hex(random_bytes(16));
        Session::put('oauth_state', $state);
        Session::put('shop', $shop);

        // Build the authorization URL
        $authUrl = "https://{$shop}/admin/oauth/authorize?" . http_build_query([
            'client_id' => $this->shopifyClientId,
            'scope' => $this->shopifyScopes,
            'redirect_uri' => $this->shopifyRedirectUri,
            'state' => $state,
        ]);

        return redirect($authUrl);
    }

    /**
     * Handle the OAuth callback from Shopify
     */
    public function callback(Request $request)
    {
        // Verify state parameter to prevent CSRF attacks
        if ($request->state !== Session::get('oauth_state')) {
            return redirect()->route('home')->with('error', 'Invalid state parameter');
        }

        $shop = Session::get('shop');
        
        if (!$shop) {
            return redirect()->route('home')->with('error', 'Shop session not found');
        }

        // Exchange the authorization code for an access token
        try {
            $response = Http::post("https://{$shop}/admin/oauth/access_token", [
                'client_id' => $this->shopifyClientId,
                'client_secret' => $this->shopifyClientSecret,
                'code' => $request->code,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Store the access token securely (you might want to save this to your database)
                // For this example, we'll just store it in the session
                Session::put('shopify_access_token', $data['access_token']);
                Session::put('shopify_shop', $shop);
                
                return redirect()->route('home')->with('success', 'Successfully connected to Shopify!');
            } else {
                Log::error('Shopify OAuth error', [
                    'shop' => $shop,
                    'error' => $response->json()
                ]);
                
                return redirect()->route('home')->with('error', 'Failed to obtain access token from Shopify');
            }
        } catch (\Exception $e) {
            Log::error('Shopify OAuth exception', [
                'shop' => $shop,
                'exception' => $e->getMessage()
            ]);
            
            return redirect()->route('home')->with('error', 'An error occurred during OAuth process');
        }
    }

    /**
     * Make an API request to Shopify
     */
    public function makeApiRequest(Request $request)
    {
        $shop = Session::get('shopify_shop');
        $accessToken = Session::get('shopify_access_token');
        
        if (!$shop || !$accessToken) {
            return response()->json(['error' => 'Not authenticated with Shopify'], 401);
        }
        
        $endpoint = $request->input('endpoint', '/admin/api/2023-01/shop.json');
        $method = $request->input('method', 'GET');
        $data = $request->input('data', []);
        
        try {
            $response = Http::withHeaders([
                'X-Shopify-Access-Token' => $accessToken
            ])->$method("https://{$shop}{$endpoint}", $data);
            
            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            Log::error('Shopify API request error', [
                'shop' => $shop,
                'endpoint' => $endpoint,
                'exception' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Failed to make request to Shopify'], 500);
        }
    }
} 