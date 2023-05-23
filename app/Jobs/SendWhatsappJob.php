<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendWhatsappJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $phone;

    /**
     * Create a new job instance.
     */
    public function __construct($phone, private $message)
    {
        $this->onQueue('send_wa');
        $this->phone = preg_replace("/^\+?(0|62)?8/", "8", $phone);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Http::asForm()->post(config('konfer.wa_api'), [
            
            'phone' => $this->phone,
            'message' => $this->message

        ])->throw();
    }
}
