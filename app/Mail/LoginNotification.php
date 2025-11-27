<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $accountName;
    public $accountEmail;
    public $accountType;
    public $loginTime;
    public $ipAddress;

    /**
     * Create a new message instance.
     */
    public function __construct($accountName, $accountEmail, $accountType, $ipAddress = null)
    {
        $this->accountName = $accountName;
        $this->accountEmail = $accountEmail;
        $this->accountType = $accountType;
        $this->loginTime = now()->format('Y-m-d H:i:s');
        $this->ipAddress = $ipAddress ?? request()->ip();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Login Notification - Peregrine System',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.login-notification',
            with: [
                'accountName' => $this->accountName,
                'accountEmail' => $this->accountEmail,
                'accountType' => ucfirst($this->accountType),
                'loginTime' => $this->loginTime,
                'ipAddress' => $this->ipAddress,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
