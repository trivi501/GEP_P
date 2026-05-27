<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cat_tipo_contribuyente')) {
            return;
        }
        Schema::create('cat_tipo_contribuyente', function (Blueprint $table) {
            $table->smallInteger('id_tipo_contribuyente')->autoIncrement();
            $table->string('area_contribuyente', 300);
            $table->boolean('activo');
            $table->string('descripcion', 500)->nullable();
            $table->smallInteger('id_adscripcion')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cat_tipo_contribuyente');
    }
};
