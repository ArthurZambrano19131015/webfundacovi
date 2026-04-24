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
        Schema::create('lote_calidads', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_local')->unique();
            $table->boolean('synced')->default(true);
            $table->foreignId('id_registrado_por')->constrained('users')->onDelete('restrict');
            
            $table->string('codigo_lote', 50)->unique(); 
            $table->string('estado_aprobacion', 20)->default('PENDIENTE'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lote_calidads');
    }
};
