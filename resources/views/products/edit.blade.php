@extends('layouts.app')
@section('title', 'Editar producto')

@section('content')
<div class="max-w-2xl space-y-6">
  <div>
    <h1 class="text-2xl font-extrabold">Editar producto</h1>
    <p class="text-slate-600">{{ $product->name }}</p>
  </div>

  @if(session('error'))
  <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl">
    {{ session('error') }}
  </div>
  @endif

  <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
    <form method="POST" action="{{ route('products.update', $product) }}" class="space-y-4" id="product-form" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div>
        <label class="text-sm font-bold text-slate-700">Nombre</label>
        <input name="name" id="product-name" value="{{ old('name', $product->name) }}"
               class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" required>
        @error('name') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="text-sm font-bold text-slate-700">Categoría</label>
        <input name="category" id="product-category" value="{{ old('category', $product->category) }}"
               class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
        @error('category') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="text-sm font-bold text-slate-700">Tipo de venta</label>
        <select name="sale_type" id="sale_type" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" required onchange="togglePiecesField()">
          <option value="menudeo" {{ old('sale_type', $product->sale_type) == 'menudeo' ? 'selected' : '' }}>Menudeo</option>
          <option value="mayoreo" {{ old('sale_type', $product->sale_type) == 'mayoreo' ? 'selected' : '' }}>Mayoreo</option>
        </select>
        @error('sale_type') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div id="pieces_field" class="{{ old('sale_type', $product->sale_type) == 'mayoreo' ? '' : 'hidden' }}">
        <label class="text-sm font-bold text-slate-700">Piezas por paquete/caja</label>
        <input name="pieces_per_package" type="number" min="1" value="{{ old('pieces_per_package', $product->pieces_per_package) }}"
               class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Ej. 12, 24, 30...">
        @error('pieces_per_package') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="text-sm font-bold text-slate-700">Descripción</label>
        <textarea name="description" id="product-description" rows="2"
               class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" 
               placeholder="Ej. paleta de fresa con trozos de fruta natural...">{{ old('description', $product->description) }}</textarea>
        @error('description') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Sección de imagen con tabs --}}
      <div class="border border-dashed border-slate-300 rounded-xl p-4 bg-slate-50">
        <label class="text-sm font-bold text-slate-700 block mb-3">🎨 Imagen del producto</label>
        @if($product->image)
        <div class="mb-4 p-3 bg-white rounded-lg border border-slate-200">
          <p class="text-xs text-slate-500 mb-2">Imagen actual:</p>
          <img src="{{ $product->image_url }}" alt="Imagen actual" class="w-32 h-32 object-cover rounded-lg mx-auto">
        </div>
        @endif
        <input type="file" name="image_file" id="image-file" accept="image/*"
               class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-violet-100 file:text-violet-700 hover:file:bg-violet-200">
        <p class="text-xs text-slate-400 mt-2">PNG, JPG o WEBP. Máximo 2MB.</p>
        <div id="upload-preview-container" class="hidden mt-3">
          <img id="upload-preview" src="" alt="Vista previa" class="w-full max-w-xs mx-auto rounded-xl shadow-lg">
          <p class="text-center text-xs text-slate-500 mt-2">Nueva imagen a subir</p>
        </div>
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

<script>
// Mostrar/ocultar campo de piezas
function togglePiecesField() {
    const saleType = document.getElementById('sale_type').value;
    const piecesField = document.getElementById('pieces_field');
    if (saleType === 'mayoreo') {
        piecesField.classList.remove('hidden');
    } else {
        piecesField.classList.add('hidden');
    }
}

// Preview de imagen subida
document.getElementById('image-file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('upload-preview').src = e.target.result;
            document.getElementById('upload-preview-container').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
