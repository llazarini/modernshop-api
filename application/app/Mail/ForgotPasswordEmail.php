<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this
            ->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'))
            ->subject("Esqueceu a senha?")
            ->to($this->user->email)
            ->bcc(env('MAIL_ADMIN1'), env('MAIL_ADMIN2'))
            ->view('mail.forgot_password', ['url' => env('APP_STORE_URL') . '/user/password/' . $this->user->remember_token]);
    }
}
