<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_historico_predios', function (Blueprint $table) {
            $table->string('id_historico', 64)->primary();
            $table->string('id_predio', 64);
            $table->string('campo_modificado', 100);
            $table->text('valor_anterior')->nullable();
            $table->text('valor_nuevo')->nullable();
            $table->string('id_usuario_modifica', 30)->nullable();
            $table->datetime('fecha_modificacion');
            $table->string('tipo_operacion', 20)->comment('CREATE, UPDATE, DELETE');

            $table->index('id_predio');
            $table->index('fecha_modificacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_historico_predios');
    }
};
