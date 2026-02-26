@extends('layouts.app')

@section('title', 'Productos')

@section('content')
<div class="space-y-6">

  <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
    <div>
      <h1 class="text-2xl sm:text-3xl font-extrabold">Productos 🧊</h1>
      <p class="text-slate-600">Admin/Gerente: alta, edición y entrada de mercancía.</p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('products.create') }}"
         class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-extrabold hover:bg-emerald-700 transition">
        + Nuevo producto
      </a>
      <a href="{{ route('panel.index') }}"
         class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold hover:bg-slate-50 transition">
        ← Volver
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold">
      {{ session('success') }}
    </div>
  @endif
  @if($errors->any())
    <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 font-semibold">
      Revisa el formulario: hay errores.
    </div>
  @endif

  {{-- Entrada de mercancía --}}
  <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
    <h2 class="text-lg font-extrabold mb-3">Entrada de mercancía 📦</h2>

    <form method="POST" action="{{ route('inventory.entry') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
      @csrf

      <div>
        <label class="text-sm font-bold text-slate-700">Producto</label>
        <select name="product_id" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" required>
          <option value="">-- Selecciona --</option>
          @foreach($products as $p)
            <option value="{{ $p->id }}">{{ $p->name }} (Stock: {{ $p->stock }})</option>
          @endforeach
        </select>
        @error('product_id') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="text-sm font-bold text-slate-700">Cantidad</label>
        <input type="number" name="quantity" min="1" value="1"
               class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" required>
        @error('quantity') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="flex items-end">
        <button type="submit"
                class="w-full px-5 py-3 rounded-2xl bg-slate-900 text-white font-extrabold hover:bg-slate-800 transition">
          Registrar entrada
        </button>
      </div>
    </form>
  </div>

  {{-- Tabla de productos --}}
  <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-extrabold">Lista</h2>
      <span class="text-sm text-slate-600">Total: <b>{{ $products->count() }}</b></span>
    </div>

    @if($products->isEmpty())
      <p class="text-slate-600 mt-4">No hay productos aún.</p>
    @else
      <div class="mt-4 overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left text-slate-600 border-b">
              <th class="py-2 pr-4">Nombre</th>
              <th class="py-2 pr-4">Categoría</th>
              <th class="py-2 pr-4">Precio</th>
              <th class="py-2 pr-4">Stock</th>
              <th class="py-2 pr-4">Activo</th>
              <th class="py-2 pr-4 text-right">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($products as $p)
              <tr class="border-b last:border-b-0">
                <td class="py-3 pr-4 font-bold">{{ $p->name }}</td>
                <td class="py-3 pr-4">{{ $p->category ?? '—' }}</td>
                <td class="py-3 pr-4">${{ number_format($p->price, 2) }}</td>
                <td class="py-3 pr-4">{{ $p->stock }}</td>
                <td class="py-3 pr-4">
                  <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold
                    {{ $p->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                    {{ $p->is_active ? 'Sí' : 'No' }}
                  </span>
                </td>
                <td class="py-3 pr-4">
                  <div class="flex justify-end">
                    <a href="{{ route('products.edit', $p) }}"
                       class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold hover:bg-slate-50 transition">
                      Editar
                    </a>
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
