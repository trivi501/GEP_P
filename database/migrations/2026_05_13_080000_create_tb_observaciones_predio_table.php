<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_observaciones_predio', function (Blueprint $table) {
            $table->string('id_observacion', 64)->primary();
            $table->string('id_predio', 64);
            $table->text('observacion');
            $table->datetime('fecha_registro');
            $table->string('id_usuario', 30)->nullable();

            $table->index('id_predio');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_observaciones_predio');
    }
};
