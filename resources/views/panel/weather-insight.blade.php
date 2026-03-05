@extends('layouts.app')

@section('title','Análisis del clima')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

<h1 class="text-3xl font-extrabold">
    Análisis de clima 🌤️
</h1>

@if(!$weather)

<div class="bg-yellow-50 border border-yellow-200 p-4 rounded-xl">
No hay datos del clima aún.
</div>

@else

<div class="bg-white border rounded-xl p-5 shadow">

<div class="text-lg font-bold">
Temperatura actual
</div>

<div class="text-4xl font-extrabold mt-2">
{{ $weather->temp }}°C
</div>

<div class="text-gray-500">
{{ $weather->condition }}
</div>

</div>

<div class="bg-blue-50 border border-blue-200 p-5 rounded-xl">

<h2 class="font-bold mb-2">
Recomendación del sistema
</h2>

<p class="text-lg">
{{ $recommendation }}
</p>

</div>

@endif

</div>

@endsection
