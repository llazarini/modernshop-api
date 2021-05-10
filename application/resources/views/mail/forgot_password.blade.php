@extends('mail/template')
@section('title')
    Recuperação de Senha
@endsection
@section('content')
    <p>
        Olá, enviamos este e-mail pois você solicitou uma redefinição de senha. Se não foi você, por favor, ignore este e-mail.<br>
        Para redefinir sua senha basta clicar no botão abaixo ou acessar a URL abaixo.<br>
    </p>
    <a class="action-button" href="{{ $url }}">Redefinir senha</a> <br>
    <p class="float-left">Ou copie e cole a URL abaixo:<br></p>
    <a class="float-left" style="font-size: 10px" href="{{ $url }}">{{ $url }}</a>
@endsection
