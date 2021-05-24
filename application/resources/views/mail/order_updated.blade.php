@extends('mail/template')
@section('title')
    Seu pedido foi atualizado
@endsection
@section('content')
    <p>
        O seu pedido de código #{{ $order->id }} foi atualizado para o status de {{ $order->payment_status->name }}.<br>
        <br>
        Em caso de dúvidas entre em contato conosco em nosso whatsapp {{ env('STORE_PHONE') }} ou através do e-mail {{ env('STORE_EMAIL') }}.
    </p>
@endsection
