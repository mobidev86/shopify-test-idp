<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development/)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

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
