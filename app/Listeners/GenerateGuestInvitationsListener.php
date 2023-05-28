<?php

namespace App\Listeners;

use App\Events\GenerateGuestInvitationsEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GenerateGuestInvitationsListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(GenerateGuestInvitationsEvent $event): void
    {
        //
    }
}
