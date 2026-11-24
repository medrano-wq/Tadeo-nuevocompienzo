@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
    <h1>Iniciar sesión</h1>

    <form action="{{ route('login') }}" method="POST">
        @csrf

        <div class="field">
            <label for="email">Correo electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <div class="field">
            <label for="password">Contraseña</label>
            <input id="password" type="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-primary">Entrar</button>
    </form>
@endsection