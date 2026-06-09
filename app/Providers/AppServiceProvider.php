<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event; // Import the Event facade
use Slides\Saml2\Events\SignedIn;     // Import the package's event
use App\Listeners\SamlLoginListener;  // Import your listener

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        // Register the SAML SignedIn event to your custom listener
        Event::listen(
            SignedIn::class,
            SamlLoginListener::class,
        );
    }
}
