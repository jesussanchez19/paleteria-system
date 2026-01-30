@extends('layouts.app')

@section('title', 'Vendedores')

@section('content')
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold">Vendedores 👥</h1>
                <p class="text-slate-600">El gerente/admin puede dar de alta y eliminar vendedores.</p>
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

        {{-- Mensajes --}}
        @if (session('success'))
            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 font-semibold">
                {{ session('error') }}
            </div>
        @endif

        {{-- Form alta vendedor --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold mb-3">Agregar vendedor</h2>

            <form method="POST" action="{{ route('vendedores.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @csrf

                <div>
                    <label class="text-sm font-bold text-slate-700">Nombre</label>
                    <input name="name" value="{{ old('name') }}"
                        class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Ej. Juan Pérez"
                        required>
                    @error('name')
                        <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-sm font-bold text-slate-700">Correo</label>
                    <input name="email" type="email" value="{{ old('email') }}"
                        class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="ejemplo@correo.com"
                        required>
                    @error('email')
                        <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-sm font-bold text-slate-700">Contraseña</label>
                    <input name="password" type="password" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"
                        placeholder="******" required>
                    @error('password')
                        <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-3">
                    <button type="submit"
                        class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold transition">
                        Crear vendedor
                    </button>
                </div>
            </form>
        </div>

        {{-- Lista vendedores --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-extrabold">Lista de vendedores</h2>
                <span class="text-sm text-slate-600">Total: <b>{{ $vendedores->count() }}</b></span>
            </div>

            @if ($vendedores->isEmpty())
                <p class="text-slate-600 mt-4">Aún no hay vendedores registrados.</p>
            @else
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-slate-600 border-b">
                                <th class="py-2 pr-4">Nombre</th>
                                <th class="py-2 pr-4">Email</th>
                                <th class="py-2 pr-4">Rol</th>
                                <th class="py-2 pr-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vendedores as $v)
                                <tr class="border-b last:border-b-0">
                                    <td class="py-3 pr-4 font-bold">{{ $v->name }}</td>
                                    <td class="py-3 pr-4">{{ $v->email }}</td>
                                    <td class="py-3 pr-4">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full bg-slate-100 border border-slate-200 text-xs font-bold">
                                            {{ $v->role }}
                                        </span>
                                    </td>
                                    <td class="py-3 pr-4">
                                        <div class="flex justify-end gap-2 items-center">

                                            {{-- Switch Activar / Desactivar --}}
                                            <form method="POST" action="{{ route('vendedores.toggle', $v->id) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <label class="flex items-center cursor-pointer gap-2" {{ auth()->id() === $v->id ? 'style=opacity:0.5;cursor:not-allowed;' : '' }}>
                                                    <input type="checkbox" name="toggle" class="sr-only" 
                                                        {{ $v->is_active ? 'checked' : '' }}
                                                        {{ auth()->id() === $v->id ? 'disabled' : '' }}
                                                        onchange="this.form.submit()">
                                                    <span class="relative inline-block w-10 h-6 transition-colors rounded-full {{ $v->is_active ? 'bg-emerald-600' : 'bg-slate-300' }}">
                                                        <span class="absolute top-0.5 left-0.5 inline-block w-5 h-5 transition-transform transform bg-white rounded-full {{ $v->is_active ? 'translate-x-4' : '' }}"></span>
                                                    </span>
                                                </label>
                                            </form>

                                            {{-- Editar --}}
                                            <a href="{{ route('vendedores.edit', $v->id) }}"
                                               class="px-4 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white font-extrabold transition"
                                               style="text-decoration:none;">
                                                Editar
                                            </a>

                                            {{-- Eliminar --}}
                                            <form method="POST" action="{{ route('vendedores.destroy', $v->id) }}"
                                                onsubmit="return confirm('¿Eliminar vendedor: {{ $v->name }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold transition"
                                                    {{ auth()->id() === $v->id ? 'disabled' : '' }}>
                                                    Eliminar
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
@endsection
