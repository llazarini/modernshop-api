<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderSuccessEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $order;

    public function __construct(Order $order)
    {
        $this->order = Order::with([
                'order_products.product',
                'user',
                'user_address' => function($with) {
                    $with->with(['city', 'state']);
                }
            ])
            ->find($order->id);
    }

    public function build()
    {
        return $this
            ->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'))
            ->subject("Agradecemos muito pela sua compra!")
            ->to($this->order->user->email)
            ->bcc(env('MAIL_ADMIN1'), env('MAIL_ADMIN2'))
            ->view('mail.order_success');
    }
}
