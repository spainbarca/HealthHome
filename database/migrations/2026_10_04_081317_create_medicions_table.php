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
        Schema::create('mediciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sesion_control_id')
                ->constrained('sesiones_control')
                ->cascadeOnDelete();

            $table->unsignedInteger('numero_medicion')->default(1);

            $table->dateTime('fecha_hora');

            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mediciones');
    }
};
