<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderUpdatedEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $order;

    public function __construct(Order $order)
    {
        $this->order = Order::with([
                'payment_status',
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
            ->subject("O status do seu pedido foi atualizado")
            ->to($this->order->user->email)
            ->bcc(env('MAIL_ADMIN1'), env('MAIL_ADMIN2'))
            ->view('mail.order_updated', [
                'order' => $this->order
            ]);
    }
}
