<?php

namespace App\Services;

// use App\Mail\GlobalEmail;
use App\Mail\sendEmail;
use Illuminate\Support\Facades\Mail;

class SendEmailServices
{
    /**
     * Kirim email global dengan template dan data fleksibel.
     */
    public function send(string $to, string $bladeTemplate, array $data): bool
    {
        try {
            Mail::to($to)->send(new sendEmail($bladeTemplate, $data));
            return true;
        } catch (\Exception $e) {
            \Log::error("EmailService Error: " . $e->getMessage());
            return false;
        }
    }
}
