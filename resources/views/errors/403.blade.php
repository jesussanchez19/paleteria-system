@extends('layouts.app')

@section('title', 'No autorizado')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
    <div class="text-7xl mb-4">🚫</div>
    <h1 class="text-3xl font-extrabold mb-2 text-pink-600">Acceso no autorizado</h1>
    <p class="text-lg text-slate-700 mb-6">No tienes permisos para ver esta página.<br>Si crees que esto es un error, contacta a un administrador.</p>
        @php
            $user = auth()->user();
            if ($user && $user->isVendedor()) {
                $backUrl = route('pos.index');
            } elseif ($user && ($user->isGerente() || $user->isAdmin())) {
                $backUrl = route('panel.index');
            } else {
                $backUrl = route('catalogo.index');
            }
        @endphp
        <a href="{{ $backUrl }}" class="px-6 py-3 rounded-xl bg-pink-500 text-white font-bold hover:bg-pink-600 transition">← Volver</a>
</div>
@endsection
