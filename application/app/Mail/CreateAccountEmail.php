<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CreateAccountEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function build()
    {
        $user = $this->user;
        return $this
            ->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'))
            ->subject("Conta criada com sucesso!")
            ->to($user->email)
            ->bcc(env('MAIL_ADMIN1'), env('MAIL_ADMIN2'))
            ->view('mail.create_account');
    }
}
