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

            $table->string('tipo_documento', 10)->nullable();
            $table->string('numero_documento', 20)->nullable();
            $table->string('parentesco', 50)->nullable();

            $table->unique(
                [
                    'tipo_documento',
                    'numero_documento',
                ],
                'personas_tipo_documento_numero_unique'
            );

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
