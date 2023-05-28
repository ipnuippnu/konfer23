<?php

namespace App\Providers;

use App\Events\GenerateGuestInvitationsEvent;
use App\Events\UpdateAllIdCardEvent;
use App\Listeners\GenerateGuestInvitationsListener;
use App\Listeners\UpdateAllIdCardListener;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        UpdateAllIdCardEvent::class => [
            UpdateAllIdCardListener::class
        ],

        GenerateGuestInvitationsEvent::class => [
            GenerateGuestInvitationsListener::class
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    
        Event::listen(Login::class, function(Login $event) {
            activity()
                ->useLog('auth')
                ->withProperties([
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ])
                ->log('Panitia login');
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
