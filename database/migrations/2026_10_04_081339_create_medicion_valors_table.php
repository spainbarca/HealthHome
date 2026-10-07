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
        Schema::create('medicion_valores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('medicion_id')
                ->constrained('mediciones')
                ->cascadeOnDelete();

            $table->foreignId('tipo_parametro_id')
                ->constrained('tipos_parametro');

            $table->decimal('valor', 12, 3)->nullable();

            $table->string('valor_texto')->nullable();

            $table->timestamps();

            $table->unique([
                'medicion_id',
                'tipo_parametro_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicion_valores');
    }
};
