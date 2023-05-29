<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendWhatsappJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    private $phone;

    /**
     * Create a new job instance.
     */
    public function __construct($phone, private $message, private $image = null)
    {
        $this->onQueue('send_wa');
        $this->phone = preg_replace("/^\+?(0|62)?8/", "8", $phone);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            // Determine if the batch has been cancelled...
 
            return;
        }

        Http::asForm()->post(config('konfer.wa_api'), [
            
            'phone' => $this->phone,
            'message' => $this->message,
            'image' => $this->image

        ])->throw();
    }
}
