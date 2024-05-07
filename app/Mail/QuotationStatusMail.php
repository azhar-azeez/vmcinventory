<?php

namespace App\Mail;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Dompdf\Dompdf;
use Dompdf\Options;

class QuotationStatusMail extends Mailable
{
    use Queueable, SerializesModels;


    public $quotation;
    public $pdfContent;
    /**
     * Create a new message instance.
     * 
     * @param Quotation $quotation
     */
    public function __construct($quotation, $pdfContent) 
    {
        $this->quotation = $quotation;
        $this->pdfContent = $pdfContent;
    }

        /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Attach the generated PDF to the email
        return $this->subject('Quotation Status Mail')
                    ->attachData($this->pdfContent, 'customer_quotation.pdf', [
                        'mime' => 'application/pdf',
                    ])
                    ->view('emails.quotation_status'); // Use this to send the HTML version of the email
    }
}
