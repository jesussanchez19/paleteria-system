@extends('layouts.app')

@section('title','IA')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold">Inteligencia Artificial 🤖</h1>
            <p class="text-slate-600">Aquí irá el chatbot para sugerencias de stock y clima.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('panel.index') }}"
               class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold text-slate-800 hover:bg-slate-50 transition">
                ← Volver al panel
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="px-4 py-2 rounded-xl bg-slate-900 text-white font-extrabold hover:bg-slate-800 transition">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
