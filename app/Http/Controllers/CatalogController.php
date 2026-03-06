<?php

namespace App\Http\Controllers;

use App\Models\Product;

class CatalogController extends Controller
{
    public function index()
    {
        $products = Product::query()
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        // Productos agrupados por categoría
        $productsByCategory = $products->groupBy('category');

        // Productos destacados (los más caros o primeros 4)
        $featured = $products->sortByDesc('price')->take(4);

        // Información del negocio
        $business = [
            'name' => app_setting('business_name', 'Creamyx'),
            'phone' => app_setting('business_phone', ''),
            'address' => app_setting('business_address', ''),
            'city' => app_setting('business_city', 'México'),
            'slogan' => app_setting('business_slogan', '¡Tradición y sabor en cada mordida!'),
            'open_time' => app_setting('open_time', '08:00'),
            'close_time' => app_setting('close_time', '20:00'),
            'facebook' => app_setting('social_facebook', ''),
            'instagram' => app_setting('social_instagram', ''),
            'whatsapp' => app_setting('social_whatsapp', ''),
            'lat' => app_setting('business_lat', '19.4326'),
            'lng' => app_setting('business_lng', '-99.1332'),
        ];

        // Categorías disponibles
        $categories = $productsByCategory->keys();

        return view('catalogo.index', compact(
            'products', 
            'productsByCategory', 
            'featured', 
            'business',
            'categories'
        ));
    }
}
