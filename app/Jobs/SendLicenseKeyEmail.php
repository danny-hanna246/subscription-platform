<?php

namespace App\Jobs;

use App\Mail\LicenseKeyMail;
use App\Models\License;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendLicenseKeyEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $license;

    public function __construct(License $license)
    {
        $this->license = $license;
    }

    public function handle(): void
    {
        Mail::to($this->license->subscription->customer->email)
            ->send(new LicenseKeyMail($this->license));
    }
}