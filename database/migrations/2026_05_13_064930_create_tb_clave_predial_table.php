<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tb_clave_predial')) {
            return;
        }
        Schema::create('tb_clave_predial', function (Blueprint $table) {
            $table->string('id_clave_predial', 64)->primary();
            $table->integer('id_poblacion')->nullable();
            $table->integer('id_seccion')->nullable();
            $table->integer('id_manzana')->nullable();
            $table->integer('id_lote')->nullable();
            $table->string('subLote', 2)->nullable();
            $table->string('Parcela', 6)->nullable();
            $table->unsignedTinyInteger('id_tipo_predio');
            $table->string('prefijo', 6)->nullable();
            $table->string('clave_predial_completa', 18)->nullable();
            $table->string('manzana_rustico', 3)->nullable();
            $table->string('lote_rustico', 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_clave_predial');
    }
};
