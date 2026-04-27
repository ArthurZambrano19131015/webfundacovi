<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('guia_informativas', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_local')->unique();
            $table->boolean('synced')->default(true);

            $table->foreignId('id_autor')->constrained('users')->onDelete('cascade');
            $table->string('titulo', 150);
            $table->longText('contenido');

            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('guia_informativas');
    }
};
