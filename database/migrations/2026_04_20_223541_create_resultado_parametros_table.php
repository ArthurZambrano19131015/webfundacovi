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
        Schema::create('resultado_parametros', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_local')->unique();
            $table->boolean('synced')->default(true);
            $table->foreignId('id_lote')->constrained('lote_calidads')->onDelete('cascade');
            $table->foreignId('id_estandar')->constrained('estandar_calidads')->onDelete('restrict');
            $table->decimal('valor_obtenido', 8, 2);
            $table->boolean('cumple_estandar'); 

            $table->timestamps();
            $table->unique(['id_lote', 'id_estandar']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultado_parametros');
    }
};
