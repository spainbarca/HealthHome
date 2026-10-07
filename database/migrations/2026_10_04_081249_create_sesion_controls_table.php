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
        Schema::create('sesiones_control', function (Blueprint $table) {
            $table->id();

            $table->foreignId('persona_id')
                ->constrained('personas');

            $table->foreignId('tipo_control_id')
                ->constrained('tipos_control');

            $table->foreignId('dispositivo_id')
                ->nullable()
                ->constrained('dispositivos');

            $table->dateTime('fecha_hora');

            $table->string('origen', 30)->default('DOMICILIARIO');

            $table->string('estado_previo')->nullable();

            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesiones_control');
    }
};
