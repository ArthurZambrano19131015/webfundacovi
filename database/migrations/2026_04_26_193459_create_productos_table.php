<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_local')->unique();
            $table->boolean('synced')->default(true);
            $table->foreignId('id_apiario')->constrained('apiarios')->onDelete('restrict');
            $table->string('nombre', 100);
            $table->decimal('precio', 10, 2);
            $table->longText('foto')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('estado_activo')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
