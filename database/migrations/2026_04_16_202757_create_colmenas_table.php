<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colmenas', function (Blueprint $table) {
            $table->id(); 
            $table->uuid('id_local')->unique();
            $table->boolean('synced')->default(true);

            $table->foreignId('id_apiario')->constrained('apiarios')->onDelete('restrict');

            $table->string('identificador', 50); 
            $table->string('tipo_colmena', 50)->nullable();
            $table->date('fecha_instalacion');
            $table->boolean('estado_activo')->default(true);

            $table->timestamps();

            $table->unique(['id_apiario', 'identificador']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colmenas');
    }
};