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
        Schema::create('cosechas', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_local')->unique();
            $table->boolean('synced')->default(true);
            $table->foreignId('id_colmena')->constrained('colmenas')->onDelete('restrict');
            $table->foreignId('id_lote')->nullable()->constrained('lote_calidads')->onDelete('set null');
            $table->date('fecha_recoleccion');
            $table->decimal('cantidad_kg', 8, 2);
            $table->text('novedades')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cosechas');
    }
};
