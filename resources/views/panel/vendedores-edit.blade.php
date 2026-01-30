@extends('layouts.app')

@section('title', 'Editar vendedor')

@section('content')
<div class="space-y-6 max-w-xl mx-auto">
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold">Editar vendedor</h1>
        <a href="{{ route('vendedores.index') }}" class="text-blue-600 hover:underline">← Volver a la lista</a>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <form method="POST" action="{{ route('vendedores.update', $user->id) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="text-sm font-bold text-slate-700">Nombre</label>
                <input name="name" value="{{ old('name', $user->name) }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" required>
                @error('name')
                    <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-sm font-bold text-slate-700">Correo</label>
                <input name="email" type="email" value="{{ old('email', $user->email) }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" required>
                @error('email')
                    <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-sm font-bold text-slate-700">Contraseña (dejar vacío para no cambiar)</label>
                <input name="password" type="password" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="******">
                @error('password')
                    <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <button type="submit" class="w-full px-5 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold transition">Actualizar vendedor</button>
            </div>
        </form>
    </div>
</div>
@endsection
