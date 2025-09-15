<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class sendEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public $data;
    protected $bladeTemplate;
    public function __construct(string $bladeTemplate, array $data)
    {
        $this->bladeTemplate = $bladeTemplate;
        $this->data = $data;
    }

   public function build()
    {
        return $this->view($this->bladeTemplate)
                    ->with($this->data)
                    ->subject($this->data['subject'] ?? 'No Subject');
    }
   
}
