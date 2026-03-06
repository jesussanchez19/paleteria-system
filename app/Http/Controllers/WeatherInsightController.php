<?php

namespace App\Http\Controllers;

use App\Models\WeatherSnapshot;
use App\Models\Sale;
use App\Models\Product;
use App\Services\GeminiService;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class WeatherInsightController extends Controller
{
    public function index(GeminiService $gemini, WeatherService $weatherService)
    {
        $city = app_setting('business_city', 'Mexico City');
        $today = now()->toDateString();

        // Clima de hoy
        $weather = WeatherSnapshot::where('date', $today)
            ->where('city', $city)
            ->first();

        // Si no hay snapshot de hoy, obtenerlo en vivo
        if (!$weather) {
            $data = $weatherService->getCurrentByCity($city);
            
            if ($data['ok']) {
                $weather = WeatherSnapshot::create([
                    'date' => $today,
                    'city' => $city,
                    'temp' => (float) $data['temp'],
                    'condition' => (string) $data['condition'],
                    'raw' => $data['raw'],
                ]);

                audit_log('weather.snapshot.ondemand', 'weather', null, [
                    'city' => $city,
                    'temp' => $data['temp'],
                    'condition' => $data['condition'],
                    'source' => 'weather_insight',
                ]);
            }
        }

        // Historial de clima últimos 7 días con ventas
        $historialClima = WeatherSnapshot::where('city', $city)
            ->where('date', '>=', now()->subDays(7)->toDateString())
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($w) {
                $ventas = Sale::whereDate('created_at', $w->date)->sum('total');
                return [
                    'date' => $w->date,
                    'temp' => $w->temp,
                    'condition' => $w->condition,
                    'icon' => $w->icon,
                    'ventas' => $ventas,
                ];
            });

        // Calcular correlación clima-ventas
        $correlacion = $this->calcularCorrelacion($historialClima);

        // Recomendación básica
        $recommendation = $this->getRecomendacionBasica($weather);

        // Productos sensibles al clima
        $productosSensibles = $this->getProductosSensibles();

        // Análisis IA (con cache de 10 minutos)
        $analisisIA = null;
        if ($weather) {
            $analisisIA = Cache::remember('weather_ia_' . now()->format('Y-m-d-H'), 600, function () use ($gemini, $weather, $historialClima, $correlacion) {
                return $this->generarAnalisisIA($gemini, $weather, $historialClima, $correlacion);
            });
        }

        // Alertas climáticas
        $alertas = $this->generarAlertasClimaticas($weather, $historialClima);

        // Recomendaciones de producción
        $recomendacionesProduccion = $this->getRecomendacionesProduccion($weather);

        return view('panel.weather-insight', [
            'weather' => $weather,
            'recommendation' => $recommendation,
            'historialClima' => $historialClima,
            'correlacion' => $correlacion,
            'analisisIA' => $analisisIA,
            'alertas' => $alertas,
            'recomendacionesProduccion' => $recomendacionesProduccion,
            'productosSensibles' => $productosSensibles,
        ]);
    }

    private function getRecomendacionBasica($weather): ?string
    {
        if (!$weather) return null;

        if ($weather->temp >= 30) {
            return "🔥 Día muy caluroso. Se recomienda aumentar producción de paletas y aguas.";
        } elseif ($weather->temp >= 25) {
            return "☀️ Día cálido. Se recomienda aumentar bebidas frías.";
        } elseif ($weather->temp <= 20) {
            return "🌥️ Día fresco. Posible baja en ventas de paletas.";
        }
        return "🌤️ Clima templado. Ventas normales esperadas.";
    }

    private function calcularCorrelacion($historial): array
    {
        if ($historial->count() < 3) {
            return ['tipo' => 'sin_datos', 'mensaje' => 'Se necesitan más datos'];
        }

        $tempAlta = $historial->where('temp', '>=', 28);
        $tempBaja = $historial->where('temp', '<', 25);

        $ventasPromedioAlta = $tempAlta->avg('ventas') ?? 0;
        $ventasPromedioBaja = $tempBaja->avg('ventas') ?? 0;

        if ($ventasPromedioAlta > $ventasPromedioBaja * 1.2) {
            return [
                'tipo' => 'positiva',
                'mensaje' => 'Las ventas aumentan con el calor',
                'porcentaje' => round((($ventasPromedioAlta - $ventasPromedioBaja) / max($ventasPromedioBaja, 1)) * 100),
            ];
        } elseif ($ventasPromedioBaja > $ventasPromedioAlta * 1.1) {
            return [
                'tipo' => 'negativa',
                'mensaje' => 'Las ventas bajan con el calor',
                'porcentaje' => round((($ventasPromedioBaja - $ventasPromedioAlta) / max($ventasPromedioAlta, 1)) * 100),
            ];
        }
        return ['tipo' => 'neutral', 'mensaje' => 'El clima no afecta significativamente las ventas'];
    }

    private function getProductosSensibles(): array
    {
        // Productos que se venden más con calor vs frío
        $productosCalor = Product::whereIn('category', ['Paletas', 'Aguas', 'Helados'])
            ->where('is_active', true)
            ->select('name', 'category', 'stock')
            ->limit(5)
            ->get();

        return [
            'calor' => $productosCalor,
        ];
    }

    private function generarAnalisisIA($gemini, $weather, $historial, $correlacion): string
    {
        $historialTexto = $historial->take(5)->map(function ($h) {
            return "{$h['date']}: {$h['temp']}°C, {$h['condition']}, ventas: \${$h['ventas']}";
        })->implode("\n");

        $prompt = "
Eres el asistente IA de una paletería. Analiza el clima y su impacto en las ventas.

CLIMA ACTUAL:
- Temperatura: {$weather->temp}°C
- Condición: {$weather->condition}

HISTORIAL RECIENTE:
{$historialTexto}

CORRELACIÓN DETECTADA: {$correlacion['mensaje']}

Genera un análisis breve (3-4 oraciones) que incluya:
1. Impacto esperado del clima en ventas hoy
2. Tendencia observada en el historial
3. Una recomendación estratégica específica

Responde en español, de forma profesional y directa.
";

        return $gemini->ask($prompt);
    }

    private function generarAlertasClimaticas($weather, $historial): array
    {
        $alertas = [];

        if (!$weather) return $alertas;

        // Alerta por calor extremo
        if ($weather->temp >= 35) {
            $alertas[] = [
                'tipo' => 'danger',
                'icono' => '🌡️',
                'titulo' => 'Calor extremo: ' . $weather->temp . '°C',
                'mensaje' => 'Día de ventas muy altas probable. Asegura stock de helados y paletas.',
            ];
        } elseif ($weather->temp >= 30) {
            $alertas[] = [
                'tipo' => 'warning',
                'icono' => '☀️',
                'titulo' => 'Día caluroso: ' . $weather->temp . '°C',
                'mensaje' => 'Espera incremento en ventas de productos fríos.',
            ];
        }

        // Alerta por clima frío
        if ($weather->temp <= 15) {
            $alertas[] = [
                'tipo' => 'info',
                'icono' => '❄️',
                'titulo' => 'Día frío: ' . $weather->temp . '°C',
                'mensaje' => 'Ventas de helados probablemente bajas. Considera promociones.',
            ];
        }

        // Alerta por lluvia
        $condicionesLluvia = ['Rain', 'Drizzle', 'Thunderstorm', 'Lluvia', 'Tormenta'];
        if (in_array($weather->condition, $condicionesLluvia)) {
            $alertas[] = [
                'tipo' => 'warning',
                'icono' => '🌧️',
                'titulo' => 'Lluvia detectada',
                'mensaje' => 'El tráfico peatonal puede verse reducido.',
            ];
        }

        // Alerta de cambio brusco de temperatura
        if ($historial->count() >= 2) {
            $ayer = $historial->skip(1)->first();
            if ($ayer && abs($weather->temp - $ayer['temp']) >= 5) {
                $cambio = $weather->temp > $ayer['temp'] ? 'subió' : 'bajó';
                $alertas[] = [
                    'tipo' => 'info',
                    'icono' => '📊',
                    'titulo' => "Cambio brusco de temperatura",
                    'mensaje' => "La temperatura {$cambio} " . abs($weather->temp - $ayer['temp']) . "°C respecto a ayer.",
                ];
            }
        }

        return $alertas;
    }

    private function getRecomendacionesProduccion($weather): array
    {
        $recs = [];
        if (!$weather) return $recs;

        if ($weather->temp >= 32) {
            $recs[] = ['icono' => '🍦', 'texto' => 'Producir 50% más de paletas', 'prioridad' => 'alta'];
            $recs[] = ['icono' => '💧', 'texto' => 'Duplicar stock de aguas', 'prioridad' => 'alta'];
            $recs[] = ['icono' => '🧊', 'texto' => 'Preparar hielo adicional', 'prioridad' => 'media'];
        } elseif ($weather->temp >= 28) {
            $recs[] = ['icono' => '🍦', 'texto' => 'Producir 30% más de paletas', 'prioridad' => 'media'];
            $recs[] = ['icono' => '🥤', 'texto' => 'Incrementar bebidas frías', 'prioridad' => 'media'];
        } elseif ($weather->temp >= 25) {
            $recs[] = ['icono' => '📈', 'texto' => 'Producción normal con ligero incremento', 'prioridad' => 'baja'];
        } elseif ($weather->temp <= 20) {
            $recs[] = ['icono' => '📉', 'texto' => 'Reducir producción de helados 20%', 'prioridad' => 'media'];
            $recs[] = ['icono' => '💡', 'texto' => 'Considerar promociones 2x1', 'prioridad' => 'media'];
        }

        return $recs;
    }

    /**
     * Pregunta rápida sobre el clima (AJAX)
     */
    public function askClima(Request $request, GeminiService $gemini)
    {
        $request->validate(['question' => 'required|string|max:200']);

        $city = app_setting('business_city', 'Mexico City');
        $weather = WeatherSnapshot::where('date', now()->toDateString())
            ->where('city', $city)
            ->first();

        // Historial de clima
        $historial = WeatherSnapshot::where('city', $city)
            ->where('date', '>=', now()->subDays(7)->toDateString())
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($w) {
                $ventas = Sale::whereDate('created_at', $w->date)->sum('total');
                return "{$w->date}: {$w->temp}°C, {$w->condition}, ventas: \${$ventas}";
            })
            ->implode("\n");

        // Ventas de hoy
        $ventasHoy = Sale::whereDate('created_at', today())->sum('total');

        $prompt = "
Eres el asistente IA de una paletería. Responde preguntas sobre el clima y su impacto en ventas.

CLIMA ACTUAL:
- Ciudad: {$city}
- Temperatura: " . ($weather?->temp ?? 'No disponible') . "°C
- Condición: " . ($weather?->condition ?? 'No disponible') . "

VENTAS HOY: \${$ventasHoy}

HISTORIAL (últimos días):
{$historial}

PREGUNTA: {$request->question}

Responde en español, máximo 2-3 oraciones, de forma directa y útil.
";

        $answer = $gemini->ask($prompt);

        return response()->json([
            'success' => true,
            'answer' => $answer,
        ]);
    }
}
