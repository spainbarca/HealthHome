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
        Schema::create('tipos_parametro', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 30)->unique();

            $table->string('nombre');
            $table->string('nombre_corto')->nullable();

            $table->string('unidad', 20)->nullable();

            $table->string('tipo_dato', 20)->default('decimal');

            $table->unsignedTinyInteger('decimales')->default(0);

            $table->boolean('activo')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_parametro');
    }
};
