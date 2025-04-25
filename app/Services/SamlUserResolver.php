<?php

namespace App\Services;

use CodeGreenCreative\SamlIdp\Contracts\SamlUserResolver;
use CodeGreenCreative\SamlIdp\SamlUser;

class CustomSamlUserResolver implements SamlUserResolver
{
    public function resolveFromLoginRequest(): SamlUser
    {
        $email = session('saml_user_email');

        return new SamlUser(
            id: $email,
            attributes: [
                'email' => [$email],
                'first_name' => ['Shopify'],
                'last_name' => ['B2B'],
                'name' => ['Shopify B2B User'],
            ]
        );
    }

    public function resolveFromLogoutRequest(): ?SamlUser
    {
        return null; // optional
    }
}

