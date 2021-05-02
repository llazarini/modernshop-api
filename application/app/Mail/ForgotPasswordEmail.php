<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $email;

    public function __construct($email)
    {
        $this->email = $email;
    }

    public function build()
    {
        return $this
            ->from(env('MAIL_FROM_ADDRESS'))
            ->subject("Esqueceu a senha?")
            ->to($this->email)
            ->view('mail.forgot_password', ['code' => 123456]);
    }
}
