<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OtpEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $clientName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($clientName, $otp)
    {
        $this->otp = $otp;
        $this->clientName = $clientName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->clientName . " OTP Verification";
        return $this->from('service@service.com', $this->clientName . " APP")
                     ->subject($subject)
                     ->view('email.otp_v2', [
                        'otp' => $this->otp,
                        'clientName' => $this->clientName
                    ]
        );
    }
}
