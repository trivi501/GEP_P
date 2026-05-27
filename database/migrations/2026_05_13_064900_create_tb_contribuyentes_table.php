<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_contribuyentes', function (Blueprint $table) {
            $table->string('id_contribuyente', 64)->primary();
            $table->string('nombre', 200)->nullable();
            $table->string('primer_apellido', 150)->nullable();
            $table->string('segundo_apellido', 150)->nullable();
            $table->string('curp_contribuyente', 20)->nullable();
            $table->string('telefono', 13)->nullable();
            $table->string('correo_electronico', 100)->nullable();
            $table->datetime('fecha_alta')->nullable();
            $table->string('id_user_registra', 30)->nullable();
            $table->string('id_domicilio', 64)->nullable();
            $table->smallInteger('id_tipo_contribuyente')->nullable();
            $table->string('rfc', 15)->nullable();
            $table->boolean('activo')->nullable();
            $table->unsignedTinyInteger('id_tipo_persona')->nullable();
            $table->string('nombre_moral', 300)->nullable();
            $table->string('cuenta', 25)->nullable();
            $table->boolean('exento')->default(false);
            $table->string('nombre_completo', 500)->default('SIN NOMBRE');
            $table->string('nivel_gobierno', 50)->nullable();
            $table->smallInteger('id_cat_persona_genero')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_contribuyentes');
    }
};
