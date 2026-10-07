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
        Schema::create('tipo_control_parametros', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tipo_control_id')
                ->constrained('tipos_control');

            $table->foreignId('tipo_parametro_id')
                ->constrained('tipos_parametro');

            $table->boolean('obligatorio')->default(false);

            $table->unsignedInteger('orden')->default(0);

            $table->timestamps();

            $table->unique([
                'tipo_control_id',
                'tipo_parametro_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_control_parametros');
    }
};
