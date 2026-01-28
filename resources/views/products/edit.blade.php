@extends('layouts.app')
@section('title', 'Editar producto')

@section('content')
<div class="max-w-2xl space-y-6">
  <div>
    <h1 class="text-2xl font-extrabold">Editar producto</h1>
    <p class="text-slate-600">{{ $product->name }}</p>
  </div>

  <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
    <form method="POST" action="{{ route('products.update', $product) }}" class="space-y-4">
      @csrf
      @method('PUT')

      <div>
        <label class="text-sm font-bold text-slate-700">Nombre</label>
        <input name="name" value="{{ old('name', $product->name) }}"
               class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" required>
        @error('name') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="text-sm font-bold text-slate-700">Categoría</label>
        <input name="category" value="{{ old('category', $product->category) }}"
               class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
        @error('category') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="text-sm font-bold text-slate-700">Precio</label>
        <input name="price" type="number" step="0.01" min="0"
               value="{{ old('price', $product->price) }}"
               class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" required>
        @error('price') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <label class="flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300"
               {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
        <span class="text-sm font-bold text-slate-700">Activo</span>
      </label>

      <div class="flex gap-2">
        <button class="px-5 py-3 rounded-2xl bg-slate-900 text-white font-extrabold hover:bg-slate-800 transition">
          Guardar cambios
        </button>
        <a href="{{ route('products.index') }}"
           class="px-5 py-3 rounded-2xl bg-white border border-slate-200 font-extrabold hover:bg-slate-50 transition">
          Volver
        </a>
      </div>
    </form>
  </div>
</div>
@endsection
