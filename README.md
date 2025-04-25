# Shopify Identify Provider

This application provides an identify provider service for Shopify stores. It allows store administrators to identify customers in their store by redirecting them to this application, which then automatically logs them in based on their email address.

## Features

- Add an "Identify" button to the Shopify customer list
- Automatically identify customers based on their email address
- Secure token-based authentication flow
- One-time tokens that expire after 5 minutes

## Setup Instructions

### 1. Configure Environment Variables

Add the following environment variables to your `.env` file:

```
SHOPIFY_CLIENT_ID=your_shopify_client_id
SHOPIFY_CLIENT_SECRET=your_shopify_client_secret
SHOPIFY_REDIRECT_URI=https://your-app-domain.com/shopify/callback
SHOPIFY_SCOPES=read_customers,write_customers
```

### 2. Update the JavaScript Configuration

In `public/js/shopify-identify-provider.js`, update the `identifyProviderUrl` to match your application's domain:

```javascript
const config = {
    identifyProviderUrl: 'https://your-app-domain.com/identify',
    // ...
};
```

### 3. Deploy the Shopify App Extension

1. Create a new Shopify app in your Shopify Partner account
2. Configure the app with the settings in `shopify-app-extension/shopify.app.toml`
3. Deploy the app extension to your Shopify store

### 4. Install the App in Your Shopify Store

1. Go to your Shopify admin panel
2. Navigate to Apps > App and sales channel settings
3. Click "Add app"
4. Search for your app and install it
5. Grant the necessary permissions

## How It Works

1. When a store administrator clicks the "Identify" button next to a customer in the Shopify admin, they are redirected to this application with the customer's email and other parameters.
2. The application verifies the request using HMAC signature validation.
3. If the customer exists in the application's database, a one-time token is generated.
4. The user is redirected back to Shopify with the token.
5. Shopify verifies the token with the application and logs the customer in.

## Security Considerations

- All requests from Shopify are signed with an HMAC signature to prevent tampering.
- One-time tokens expire after 5 minutes to prevent replay attacks.
- The application only processes requests with valid timestamps (within 5 minutes).
- All sensitive data is transmitted over HTTPS.

## Troubleshooting

- **Button not appearing in customer list**: Make sure the app extension is properly installed and has the necessary permissions.
- **Authentication failures**: Check that your HMAC signatures are being generated correctly and that your timestamps are synchronized.
- **Token verification errors**: Ensure that your client secret is correctly configured and that tokens are not expired.

## License

This project is licensed under the MIT License - see the LICENSE file for details.
