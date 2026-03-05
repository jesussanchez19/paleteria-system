<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('weather_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->string('city')->index();
            $table->decimal('temp', 5, 2)->nullable();
            $table->string('condition')->nullable();
            $table->json('raw')->nullable();
            $table->timestamps();

            $table->unique(['date', 'city']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_snapshots');
    }
};
