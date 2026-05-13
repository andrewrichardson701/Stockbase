<?php

namespace App\Listeners;

use Slides\Saml2\Events\SignedIn;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SamlLoginListener
{
    public function handle(SignedIn $event)
    {
        $samlUser = $event->getSaml2User();
        $email = $samlUser->getUserId(); // Usually the NameID (Email)
        $attributes = $samlUser->getAttributes(); // Array of all mapped data

        // Log the attributes temporarily so you can see exactly what Microsoft is sending
        // Log::info('SAML Attributes:', $attributes);

        // Microsoft often sends attributes using these long schema URLs by default
        $firstName = $attributes['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname'][0] ?? 'Unknown';
        $lastName  = $attributes['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname'][0] ?? 'Unknown';
        
        // If you map custom claims in Azure, they might look simpler:

        // ---------------------------------------------------------
        // FULL CONTROL LOGIC: Update existing user OR create a new one
        // ---------------------------------------------------------
        
        $user = User::updateOrCreate(
            ['email' => $email, 'auth' => 'sso'], // Find by this
            [                    // Update or Set these values
                'name' => $firstName.' '.$lastName,
                'username' => substr($email, 0, strpos($email, '@')), // Use the part before @ as username
                // You can even set a random password since they use SSO
                'password'   => $user->password ?? bcrypt(Str::random(16)), 
                'auth' => 'sso', // Mark this user as SSO authenticated
                'email_verified_at' => now(), // Mark email as verified since it comes from a trusted IdP
                'enabled' => 1, // Ensure the user is enabled
                'password_expired' => 0 // Ensure password is not expired since they use SSO
            ]
        );

        // Log the user into Laravel
        Auth::login($user);
    }
}