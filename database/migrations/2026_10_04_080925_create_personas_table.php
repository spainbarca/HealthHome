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
        Schema::create('personas', function (Blueprint $table) {
            $table->id();

            $table->string('nombres');
            $table->string('apellido_paterno')->nullable();
            $table->string('apellido_materno')->nullable();

            $table->string('tipo_documento', 10)->nullable();
            $table->string('numero_documento', 20)->nullable();

            $table->date('fecha_nacimiento')->nullable();

            $table->enum('sexo', ['M', 'F', 'OTRO'])->nullable();

            $table->string('parentesco', 50)->nullable();

            $table->boolean('activo')->default(true);
            $table->string('foto')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};
