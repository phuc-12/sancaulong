<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pdfPath;

    public function __construct($pdfPath)
    {
        $this->pdfPath = $pdfPath;
    }

    public function build()
{
    return $this->from(config('mail.from.address'), config('mail.from.name'))
                ->subject('Hóa đơn của bạn')
                ->view('emails.invoice')
                ->attach($this->pdfPath, [
                    'as' => 'invoice.pdf',
                    'mime' => 'application/pdf',
                ]);
}

}
