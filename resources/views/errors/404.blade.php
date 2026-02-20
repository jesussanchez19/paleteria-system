@extends('layouts.app')

@section('title', 'Página no encontrada')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
    <div class="text-7xl mb-4">🔍</div>
    <h1 class="text-3xl font-extrabold mb-2 text-pink-600">Página no encontrada</h1>
    <p class="text-lg text-slate-700 mb-6">La página que buscas no existe o fue movida.<br>Verifica la URL o regresa al inicio.</p>
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
        <a href="{{ $backUrl }}" class="px-6 py-3 rounded-xl bg-pink-500 text-white font-bold hover:bg-pink-600 transition text-lg">← Volver</a>
</div>
@endsection
