@extends('layouts.app')
@section('title', 'Crear producto')

@section('content')
<div class="max-w-2xl space-y-6">
  <div>
    <h1 class="text-2xl font-extrabold">Nuevo producto</h1>
    <p class="text-slate-600">Captura datos básicos.</p>
  </div>

  <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
    <form method="POST" action="{{ route('products.store') }}" class="space-y-4" id="product-form" enctype="multipart/form-data">
      @csrf
      
      {{-- Campo oculto para la imagen generada --}}
      <input type="hidden" name="image" id="image-path" value="">

      <div>
        <label class="text-sm font-bold text-slate-700">Nombre</label>
        <input name="name" id="product-name" value="{{ old('name') }}"
               class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" required>
        @error('name') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="text-sm font-bold text-slate-700">Categoría</label>
        <input name="category" id="product-category" value="{{ old('category') }}"
               class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Ej. Paleta, Helado...">
        @error('category') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="text-sm font-bold text-slate-700">Descripción <span class="text-slate-400 font-normal">(para generar imagen con IA)</span></label>
        <textarea name="description" id="product-description" rows="2"
               class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" 
               placeholder="Ej. paleta de fresa con trozos de fruta natural, color rosa...">{{ old('description') }}</textarea>
        @error('description') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Sección de imagen --}}
      <div class="border border-dashed border-slate-300 rounded-xl p-4 bg-slate-50">
        <label class="text-sm font-bold text-slate-700 block mb-3">🎨 Imagen del producto</label>
        <input type="file" name="image_file" id="image-file" accept="image/*"
               class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-slate-900 file:text-white file:font-bold hover:file:bg-slate-800 cursor-pointer">
        <p class="text-xs text-slate-500 mt-2">PNG, JPG o WEBP. Máximo 2MB.</p>
        <div id="upload-preview-container" class="hidden mt-4">
          <img id="upload-preview" src="" alt="Vista previa" class="w-full max-w-xs mx-auto rounded-xl shadow-lg">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="text-sm font-bold text-slate-700">Precio</label>
          <input name="price" type="number" step="0.01" min="0" value="{{ old('price', 0) }}"
                 class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" required>
          @error('price') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="text-sm font-bold text-slate-700">Stock inicial</label>
          <input name="stock" type="number" min="0" value="{{ old('stock', 0) }}"
                 class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" required>
          @error('stock') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>
      </div>

      <label class="flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300">
        <span class="text-sm font-bold text-slate-700">Activo</span>
      </label>

      <div class="flex gap-2">
        <button class="px-5 py-3 rounded-2xl bg-emerald-600 text-white font-extrabold hover:bg-emerald-700 transition">
          Guardar
        </button>
        <a href="{{ route('products.index') }}"
           class="px-5 py-3 rounded-2xl bg-white border border-slate-200 font-extrabold hover:bg-slate-50 transition">
          Cancelar
        </a>
      </div>
    </form>
  </div>
</div>

<script>
// Tabs
function showTab(tab) {
    const tabUpload = document.getElementById('tab-upload');
    const tabIa = document.getElementById('tab-ia');
    const panelUpload = document.getElementById('panel-upload');
    const panelIa = document.getElementById('panel-ia');
    
    if (tab === 'upload') {
        tabUpload.classList.add('bg-slate-900', 'text-white');
        tabUpload.classList.remove('bg-white', 'text-slate-700', 'border', 'border-slate-300');
        tabIa.classList.remove('bg-slate-900', 'text-white');
        tabIa.classList.add('bg-white', 'text-slate-700', 'border', 'border-slate-300');
        panelUpload.classList.remove('hidden');
        panelIa.classList.add('hidden');
    } else {
        tabIa.classList.add('bg-slate-900', 'text-white');
        tabIa.classList.remove('bg-white', 'text-slate-700', 'border', 'border-slate-300');
        tabUpload.classList.remove('bg-slate-900', 'text-white');
        tabUpload.classList.add('bg-white', 'text-slate-700', 'border', 'border-slate-300');
        panelIa.classList.remove('hidden');
        panelUpload.classList.add('hidden');
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
        // Limpiar imagen generada si se sube una
        document.getElementById('image-path').value = '';
    }
});

// Generar con IA
document.getElementById('btn-generate-image').addEventListener('click', async function() {
    const name = document.getElementById('product-name').value.trim();
    const category = document.getElementById('product-category').value.trim();
    const description = document.getElementById('product-description').value.trim();
    
    if (!name) {
        alert('Por favor ingresa el nombre del producto primero');
        return;
    }
    
    const btn = this;
    const btnText = document.getElementById('btn-generate-text');
    const previewContainer = document.getElementById('image-preview-container');
    const preview = document.getElementById('image-preview');
    const loading = document.getElementById('image-loading');
    const imagePath = document.getElementById('image-path');
    
    btn.disabled = true;
    btnText.textContent = '⏳ Generando...';
    previewContainer.classList.add('hidden');
    loading.classList.remove('hidden');
    
    try {
        const response = await fetch('{{ route("products.generate-image") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name, category, description })
        });
        
        const data = await response.json();
        
        if (data.success) {
            preview.src = data.image_url;
            imagePath.value = data.image_path;
            previewContainer.classList.remove('hidden');
            btnText.textContent = '🔄 Regenerar';
            // Limpiar input de archivo si se genera
            document.getElementById('image-file').value = '';
            document.getElementById('upload-preview-container').classList.add('hidden');
        } else {
            alert(data.message || 'Error al generar la imagen. Intenta subir una manualmente.');
            btnText.textContent = '✨ Generar imagen con IA';
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error de conexión. Intenta subir una imagen manualmente.');
        btnText.textContent = '✨ Generar imagen con IA';
    } finally {
        loading.classList.add('hidden');
        btn.disabled = false;
    }
});
</script>
@endsection
