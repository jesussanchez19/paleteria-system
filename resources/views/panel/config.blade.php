@extends('layouts.app')

@section('title', 'Configuración')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #location-map { z-index: 0; }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-end justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold">Configuración ⚙️</h1>
            <p class="text-slate-600">Ajustes operativos del negocio.</p>
        </div>

        <a href="{{ route('panel.index') }}"
           class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold hover:bg-slate-50 transition">
            ← Volver al panel
        </a>
    </div>

    @if(session('success'))
        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('panel.config.update') }}" class="space-y-6">
        @csrf

        {{-- Datos del negocio --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold mb-4 text-slate-800">🏪 Datos del negocio</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-bold text-slate-700">Nombre del negocio</label>
                    <input type="text" name="business_name" value="{{ $data['business_name'] }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    @error('business_name') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-bold text-slate-700">Teléfono</label>
                    <input type="text" name="business_phone" value="{{ $data['business_phone'] }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="55 1234 5678">
                    @error('business_phone') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Mapa interactivo --}}
                <div class="md:col-span-2">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-bold text-slate-700">📍 Ubicación del negocio</label>
                        <button type="button" id="btn-get-location" 
                                class="text-xs px-3 py-1 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition">
                            � Actualizar ubicación
                        </button>
                    </div>
                    <p class="text-xs text-slate-500 mb-2">La ubicación guardada se muestra en el mapa de "Visítanos". Puedes hacer clic en el mapa o arrastrar el marcador para ajustarla.</p>
                    <div id="location-map" class="w-full h-64 rounded-xl border border-slate-200 z-0 relative"></div>
                    <input type="hidden" name="business_lat" id="business_lat" value="{{ $data['business_lat'] }}">
                    <input type="hidden" name="business_lng" id="business_lng" value="{{ $data['business_lng'] }}">
                </div>

                <div>
                    <label class="text-sm font-bold text-slate-700">Dirección</label>
                    <input type="text" name="business_address" id="business_address" value="{{ $data['business_address'] }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 bg-slate-50" readonly>
                    <p class="text-xs text-slate-500 mt-1">Se obtiene automáticamente del mapa</p>
                    @error('business_address') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-bold text-slate-700">Ciudad (para clima)</label>
                    <input type="text" name="business_city" id="business_city" value="{{ $data['business_city'] }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    @error('business_city') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Horarios --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold mb-4 text-slate-800">🕐 Horarios</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-bold text-slate-700">Hora de apertura</label>
                    <input type="time" name="business_open_time" value="{{ $data['business_open_time'] }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    @error('business_open_time') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-bold text-slate-700">Hora de cierre</label>
                    <input type="time" name="business_close_time" value="{{ $data['business_close_time'] }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    @error('business_close_time') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Inventario --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold mb-4 text-slate-800">📦 Inventario</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-bold text-slate-700">Umbral de stock bajo</label>
                    <input type="number" name="low_stock_threshold" value="{{ $data['low_stock_threshold'] }}" min="1"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    <p class="text-xs text-slate-500 mt-1">Se alertará cuando el stock sea menor a este número</p>
                    @error('low_stock_threshold') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3 mt-6">
                    <input type="checkbox" name="allow_negative_stock" id="allow_negative_stock" value="1"
                           {{ $data['allow_negative_stock'] == '1' ? 'checked' : '' }}
                           class="w-5 h-5 rounded border-slate-300 text-pink-500 focus:ring-pink-500">
                    <label for="allow_negative_stock" class="text-sm font-bold text-slate-700">
                        Permitir stock negativo
                    </label>
                </div>
            </div>
        </div>

        {{-- Ventas --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold mb-4 text-slate-800">💰 Ventas</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-bold text-slate-700">Tasa de impuesto (%)</label>
                    <input type="number" name="tax_rate" value="{{ $data['tax_rate'] }}" min="0" max="100" step="0.01"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    <p class="text-xs text-slate-500 mt-1">IVA u otro impuesto aplicable</p>
                    @error('tax_rate') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-bold text-slate-700">Pie de ticket</label>
                    <input type="text" name="receipt_footer" value="{{ $data['receipt_footer'] }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="¡Gracias por su compra!">
                    @error('receipt_footer') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Catálogo --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold mb-4 text-slate-800">🍦 Catálogo público</h2>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="text-sm font-bold text-slate-700">Mensaje de bienvenida</label>
                    <textarea name="catalog_message" rows="2"
                              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">{{ $data['catalog_message'] }}</textarea>
                    @error('catalog_message') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" name="show_out_of_stock" id="show_out_of_stock" value="1"
                           {{ $data['show_out_of_stock'] == '1' ? 'checked' : '' }}
                           class="w-5 h-5 rounded border-slate-300 text-pink-500 focus:ring-pink-500">
                    <label for="show_out_of_stock" class="text-sm font-bold text-slate-700">
                        Mostrar productos agotados en el catálogo
                    </label>
                </div>
            </div>
        </div>

        {{-- Notificaciones --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold mb-4 text-slate-800">📧 Notificaciones</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-3">
                    <label class="text-sm font-bold text-slate-700">📬 Email de alertas</label>
                    <input type="email" name="admin_alert_email" value="{{ $data['admin_alert_email'] }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" 
                           placeholder="gerente@tudominio.com">
                    <p class="text-xs text-slate-500 mt-1">Recibir alertas importantes del sistema</p>
                    @error('admin_alert_email') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3">
                    <select name="notify_low_stock" class="rounded-xl border border-slate-200 pl-3 pr-8 py-2">
                        <option value="1" {{ ($data['notify_low_stock'] ?? '1') == '1' ? 'selected' : '' }}>Sí</option>
                        <option value="0" {{ ($data['notify_low_stock'] ?? '1') == '0' ? 'selected' : '' }}>No</option>
                    </select>
                    <label class="text-sm font-bold text-slate-700">📦 Notificar stock bajo</label>
                </div>

                <div class="flex items-center gap-3">
                    <select name="notify_large_sales" class="rounded-xl border border-slate-200 pl-3 pr-8 py-2">
                        <option value="0" {{ ($data['notify_large_sales'] ?? '0') == '0' ? 'selected' : '' }}>No</option>
                        <option value="1" {{ ($data['notify_large_sales'] ?? '0') == '1' ? 'selected' : '' }}>Sí</option>
                    </select>
                    <label class="text-sm font-bold text-slate-700">💰 Notificar ventas grandes</label>
                </div>
            </div>
        </div>

        {{-- Botón guardar --}}
        <div>
            <button type="submit"
                    class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-slate-900 text-white font-extrabold hover:bg-slate-800 transition">
                Guardar configuración
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Coordenadas guardadas (o por defecto CDMX)
    const savedLat = parseFloat(document.getElementById('business_lat').value) || null;
    const savedLng = parseFloat(document.getElementById('business_lng').value) || null;
    const defaultLat = 19.4326;
    const defaultLng = -99.1332;
    
    // Usar coordenadas guardadas o las por defecto inicialmente
    let initialLat = savedLat || defaultLat;
    let initialLng = savedLng || defaultLng;
    
    // Inicializar mapa
    const map = L.map('location-map').setView([initialLat, initialLng], 15);
    
    // Capa de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Marcador inicial (arrastrable)
    let marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);
    
    // Función para obtener dirección desde coordenadas (geocodificación inversa)
    async function getAddressFromCoords(lat, lng) {
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`);
            const data = await response.json();
            
            if (data && data.address) {
                // Construir dirección legible
                const addr = data.address;
                let address = '';
                
                if (addr.road) address += addr.road;
                if (addr.house_number) address += ' ' + addr.house_number;
                if (addr.suburb) address += ', ' + addr.suburb;
                if (addr.city || addr.town || addr.village) {
                    const city = addr.city || addr.town || addr.village;
                    document.getElementById('business_city').value = city;
                }
                
                document.getElementById('business_address').value = address || data.display_name;
            }
        } catch (error) {
            console.error('Error obteniendo dirección:', error);
        }
    }
    
    // Actualizar campos cuando se mueve el marcador
    function updateLocation(lat, lng, fetchAddress = true) {
        document.getElementById('business_lat').value = lat.toFixed(6);
        document.getElementById('business_lng').value = lng.toFixed(6);
        if (fetchAddress) {
            getAddressFromCoords(lat, lng);
        }
    }
    
    // Mover marcador a una ubicación
    function setMarkerPosition(lat, lng, zoom = 15, fetchAddress = true) {
        marker.setLatLng([lat, lng]);
        map.setView([lat, lng], zoom);
        updateLocation(lat, lng, fetchAddress);
    }
    
    // Click en el mapa
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateLocation(e.latlng.lat, e.latlng.lng);
    });
    
    // Arrastrar marcador
    marker.on('dragend', function(e) {
        const pos = marker.getLatLng();
        updateLocation(pos.lat, pos.lng);
    });
    
    // Botón para solicitar ubicación manualmente
    document.getElementById('btn-get-location').addEventListener('click', function() {
        if (!navigator.geolocation) {
            alert('Tu navegador no soporta geolocalización');
            return;
        }
        
        this.disabled = true;
        this.textContent = '📍 Obteniendo...';
        
        navigator.geolocation.getCurrentPosition(
            (position) => {
                setMarkerPosition(position.coords.latitude, position.coords.longitude, 17, true);
                this.textContent = '✅ ¡Ubicación actualizada!';
                setTimeout(() => {
                    this.textContent = '🔄 Actualizar ubicación';
                    this.disabled = false;
                }, 2000);
            },
            (error) => {
                alert('No se pudo obtener tu ubicación. Asegúrate de dar permiso de ubicación al navegador.');
                this.textContent = '🔄 Actualizar ubicación';
                this.disabled = false;
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    });
    
    // Fix para el tamaño del mapa cuando está en un contenedor oculto inicialmente
    setTimeout(() => map.invalidateSize(), 100);
});
</script>
@endpush

@endsection
