<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_predio', function (Blueprint $table) {
            $table->string('id_predio', 64)->primary();
            $table->string('Clave_predial', 25)->nullable()->unique();
            $table->smallInteger('id_colonia')->nullable();
            $table->integer('id_calle')->nullable();
            $table->string('ubicacion', 65)->nullable();
            $table->string('codigo_postal', 10)->nullable();
            $table->string('Numero_exterior', 15)->nullable();
            $table->string('Numero_interior', 15)->nullable();
            $table->string('Referencia_entre_calle1', 250)->nullable();
            $table->string('Referncia_entre_calle2', 250)->nullable();
            $table->unsignedTinyInteger('id_zona_catastral')->nullable();
            $table->decimal('valor_catastral', 10, 2)->nullable();
            $table->decimal('valor_fiscal', 10, 2)->nullable();
            $table->unsignedTinyInteger('id_estaus_cobro_predial')->nullable();
            $table->unsignedTinyInteger('id_estado_renta')->nullable();
            $table->unsignedTinyInteger('id_regimen_propiedad')->nullable();
            $table->string('numero_de_escritura', 50)->nullable();
            $table->unsignedTinyInteger('id_titulo_propiedad')->nullable();
            $table->datetime('fecha_de_alta')->nullable();
            $table->smallInteger('año_ultimo_pago')->nullable();
            $table->string('id_clave_predial', 64)->nullable();
            $table->datetime('fecha_registro')->nullable();
            $table->decimal('latitud', 20, 16)->nullable();
            $table->decimal('longitud', 20, 16)->nullable();
            $table->unsignedTinyInteger('id_tipo_predio')->nullable();
            $table->string('id_usuario', 30)->nullable();
            $table->string('id_contribuyente', 64)->nullable();
            $table->unsignedTinyInteger('ultimo_bimestre_pago')->default(6);
            $table->decimal('superficie', 18, 4)->nullable();
            $table->decimal('construccion', 18, 4)->nullable();
            $table->decimal('importe_adeudado', 18, 4)->default(0);
            $table->smallInteger('id_cat_catastro_vinculacion_estado')->default(1);
            $table->integer('id_tb_catastro_vinculacion_detalle')->nullable();
            $table->unsignedTinyInteger('colindancias')->default(0)->nullable();
            $table->integer('gid_tb_cartografia_predio')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_predio');
    }
};
