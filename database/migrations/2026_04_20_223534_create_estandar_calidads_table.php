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
        Schema::create('estandar_calidads', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_local')->unique();
            $table->boolean('synced')->default(true);

            $table->string('parametro', 100)->unique(); 
            $table->decimal('valor_minimo', 8, 2)->nullable();
            $table->decimal('valor_maximo', 8, 2)->nullable();
            $table->string('unidad_medida', 20); 
            $table->boolean('estado_activo')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estandar_calidads');
    }
};
