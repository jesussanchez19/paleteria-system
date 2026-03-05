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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');      // e.g. "sale.created"
            $table->string('module');      // e.g. "pos", "products", "users"
            $table->string('entity_type')->nullable(); // e.g. "Sale"
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('meta')->nullable(); // extra info
            $table->timestamps();

            $table->index(['module','action']);
            $table->index(['entity_type','entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
