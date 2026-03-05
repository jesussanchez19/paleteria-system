<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function edit()
    {
        $data = [
            // Horarios
            'business_open_time' => app_setting('business_open_time', '08:30'),
            'business_close_time' => app_setting('business_close_time', '17:00'),
            
            // Negocio
            'business_name' => app_setting('business_name', 'Paletería'),
            'business_city' => app_setting('business_city', 'Mexico City'),
            'business_address' => app_setting('business_address', ''),
            'business_lat' => app_setting('business_lat', '19.4326'),
            'business_lng' => app_setting('business_lng', '-99.1332'),
            'business_phone' => app_setting('business_phone', ''),
            
            // Inventario
            'low_stock_threshold' => app_setting('low_stock_threshold', '5'),
            'allow_negative_stock' => app_setting('allow_negative_stock', '0'),
            
            // Ventas
            'tax_rate' => app_setting('tax_rate', '16'),
            'receipt_footer' => app_setting('receipt_footer', '¡Gracias por su compra!'),
            
            // Catálogo
            'catalog_message' => app_setting('catalog_message', '¡Bienvenido a nuestra paletería!'),
            'show_out_of_stock' => app_setting('show_out_of_stock', '0'),
            
            // Notificaciones
            'admin_alert_email' => app_setting('admin_alert_email'),
            'notify_low_stock' => app_setting('notify_low_stock', '1'),
            'notify_large_sales' => app_setting('notify_large_sales', '0'),
        ];

        return view('panel.config', compact('data'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            // Horarios
            'business_open_time' => ['required'],
            'business_close_time' => ['required'],
            
            // Negocio
            'business_name' => ['required', 'string', 'max:100'],
            'business_city' => ['required', 'string', 'max:100'],
            'business_address' => ['nullable', 'string', 'max:200'],
            'business_lat' => ['nullable', 'numeric'],
            'business_lng' => ['nullable', 'numeric'],
            'business_phone' => ['nullable', 'string', 'max:20'],
            
            // Inventario
            'low_stock_threshold' => ['required', 'integer', 'min:1', 'max:999'],
            'allow_negative_stock' => ['nullable'],
            
            // Ventas
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'receipt_footer' => ['nullable', 'string', 'max:200'],
            
            // Catálogo
            'catalog_message' => ['nullable', 'string', 'max:500'],
            'show_out_of_stock' => ['nullable'],
            
            // Notificaciones
            'admin_alert_email' => ['nullable', 'email', 'max:200'],
            'notify_low_stock' => ['nullable'],
            'notify_large_sales' => ['nullable'],
        ]);

        // Manejar checkboxes y selects booleanos (si no están, valor = 0)
        $validated['allow_negative_stock'] = $request->has('allow_negative_stock') ? '1' : '0';
        $validated['show_out_of_stock'] = $request->has('show_out_of_stock') ? '1' : '0';
        $validated['notify_low_stock'] = $request->input('notify_low_stock', '0');
        $validated['notify_large_sales'] = $request->input('notify_large_sales', '0');

        // Capturar valores anteriores
        $oldValues = [];
        foreach (array_keys($validated) as $key) {
            $oldValues[$key] = app_setting($key, '');
        }

        // Actualizar valores
        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => (string) $value]
            );
        }

        // Detectar cambios para auditoría
        $changes = [];
        $labels = [
            'business_open_time' => 'Hora apertura',
            'business_close_time' => 'Hora cierre',
            'business_name' => 'Nombre negocio',
            'business_city' => 'Ciudad',
            'business_address' => 'Dirección',
            'business_lat' => 'Latitud',
            'business_lng' => 'Longitud',
            'business_phone' => 'Teléfono',
            'low_stock_threshold' => 'Umbral stock bajo',
            'allow_negative_stock' => 'Permitir stock negativo',
            'tax_rate' => 'Tasa impuesto',
            'receipt_footer' => 'Pie de ticket',
            'catalog_message' => 'Mensaje catálogo',
            'show_out_of_stock' => 'Mostrar agotados',
            'admin_alert_email' => 'Email alertas',
            'notify_low_stock' => 'Notificar stock bajo',
            'notify_large_sales' => 'Notificar ventas grandes',
        ];
        
        foreach ($validated as $key => $newValue) {
            $oldValue = $oldValues[$key] ?? '';
            if ((string) $oldValue !== (string) $newValue) {
                // Formatear valores booleanos
                $oldDisplay = in_array($key, ['allow_negative_stock', 'show_out_of_stock', 'notify_low_stock', 'notify_large_sales'])
                    ? ($oldValue == '1' ? 'Sí' : 'No')
                    : ($oldValue ?: '(vacío)');
                $newDisplay = in_array($key, ['allow_negative_stock', 'show_out_of_stock', 'notify_low_stock', 'notify_large_sales'])
                    ? ($newValue == '1' ? 'Sí' : 'No')
                    : ($newValue ?: '(vacío)');
                
                $label = $labels[$key] ?? $key;
                $changes[$label] = $oldDisplay . ' → ' . $newDisplay;
            }
        }

        // Auditoría con detalles de cambios
        if (!empty($changes)) {
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'settings.updated',
                'module' => 'config',
                'entity_type' => 'Settings',
                'entity_id' => null,
                'meta' => array_merge(['_entity_name' => 'Configuración general'], $changes),
            ]);
        }

        return back()->with('success', 'Configuración guardada correctamente.');
    }
}
