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
        Schema::create('archivos_adjuntos', function (Blueprint $table) {
            $table->id();

            $table->nullableMorphs('adjuntable');

            $table->string('nombre');
            $table->string('archivo');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('tamano')->nullable();

            $table->text('descripcion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archivos_adjuntos');
    }
};
