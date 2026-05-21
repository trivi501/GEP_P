<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE tb_predio ADD FULLTEXT INDEX ft_predio_clave (Clave_predial)');
        DB::statement('ALTER TABLE tb_contribuyentes ADD FULLTEXT INDEX ft_contribuyente_cuenta (cuenta)');
        DB::statement('ALTER TABLE tb_contribuyentes ADD FULLTEXT INDEX ft_contribuyente_nombre (nombre_completo, nombre_moral)');
        DB::statement('ALTER TABLE tb_clave_predial ADD FULLTEXT INDEX ft_clave_predial_completa (clave_predial_completa)');
    }

    public function down(): void
    {
        Schema::table('tb_predio', function (Blueprint $table) {
            $table->dropIndex('ft_predio_clave');
        });
        Schema::table('tb_contribuyentes', function (Blueprint $table) {
            $table->dropIndex('ft_contribuyente_cuenta');
            $table->dropIndex('ft_contribuyente_nombre');
        });
        Schema::table('tb_clave_predial', function (Blueprint $table) {
            $table->dropIndex('ft_clave_predial_completa');
        });
    }
};
