<?php

namespace App\Mail;

use App\Models\Alumni;
use App\Models\EventRegistration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Alumni            $alumnus,
        public readonly EventRegistration $registration,
        public readonly int               $amountConfirmed,
        public readonly bool              $isAdditional,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'KZS 2002 Reunion — Payment Confirmed ✓');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment-confirmed');
    }

    public function attachments(): array
    {
        $invoiceNo = 'KZS2002-' . str_pad($this->registration->id, 5, '0', STR_PAD_LEFT);

        $pdf = Pdf::loadView('pdf.invoice', [
            'alumnus'         => $this->alumnus,
            'registration'    => $this->registration,
            'amountConfirmed' => $this->amountConfirmed,
            'isAdditional'    => $this->isAdditional,
        ])->setPaper('a4', 'portrait');

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                $invoiceNo . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
