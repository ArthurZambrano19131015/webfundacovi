<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apiarios', function (Blueprint $table) {
            $table->id(); 
            $table->uuid('id_local')->unique();
            $table->boolean('synced')->default(true);

            $table->foreignId('id_apicultor')->constrained('users')->onDelete('restrict');

            $table->string('nombre', 100);
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->string('municipio', 100)->nullable();
            $table->boolean('estado_activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apiarios');
    }
};