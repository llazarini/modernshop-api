@extends('mail/template')
@section('title')
    Muito obrigado pela sua compra!
@endsection
@section('content')
    <p>
        Ficamos muito felizes pela sua preferência,<br>
        Seu pedido foi realizado e já estamos separando ele com muito carinho.<br>
    </p>

    <h2>Compra código #{{$order->id}}</h2>
    @foreach($order->order_products as $orderProduct)
        <div>
            <b>{{ $orderProduct->product->name }}</b> - {{ $orderProduct->quantity }}x - R$ {{ $orderProduct->price }} - <b>Total</b>: R$ {{ $orderProduct->amount }}
        </div>
    @endforeach
    <div><b>Entrega:</b> {{ $order->shipment_option }} R$ {{ $order->shipment }}</div>
    <div><b>Valor Total:</b> R$ {{ $order->amount }}</div>

    <h3>Endereço de Entrega</h3>
    <p>{{ $order->user_address->street_name }} Nº{{ $order->user_address->street_number }} {{ $order->user_address->complement }}, CEP {{ $order->user_address->zip_code }}
        {{ $order->user_address->neighborhood }}, {{ $order->user_address->city->name }} - {{ $order->user_address->state->name }}</p>
@endsection
