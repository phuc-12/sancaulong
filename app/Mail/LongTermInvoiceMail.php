<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LongTermInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $fullname;
    public $pdf;

    public function __construct($fullname, $pdf)
    {
        $this->fullname = $fullname;
        $this->pdf = $pdf;
    }

    public function build()
    {
        return $this->subject('Hóa đơn hợp đồng dài hạn')
                    ->view('emails.invoice_longterm')
                    ->attachData($this->pdf->output(), 'invoice.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
