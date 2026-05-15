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
        Schema::table('tb_contribuyentes', function (Blueprint $table) {
            $table->index('nombre_completo', 'idx_contribuyentes_nombre_completo');
            $table->index('cuenta', 'idx_contribuyentes_cuenta');
            $table->index('telefono', 'idx_contribuyentes_telefono');
            $table->index('correo_electronico', 'idx_contribuyentes_correo');
            $table->index('activo', 'idx_contribuyentes_activo');
        });

        Schema::table('cat_tipo_contribuyente', function (Blueprint $table) {
            $table->index('area_contribuyente', 'idx_tipo_contribuyente_area');
        });
    }

    public function down(): void
    {
        Schema::table('tb_contribuyentes', function (Blueprint $table) {
            $table->dropIndex('idx_contribuyentes_nombre_completo');
            $table->dropIndex('idx_contribuyentes_cuenta');
            $table->dropIndex('idx_contribuyentes_telefono');
            $table->dropIndex('idx_contribuyentes_correo');
            $table->dropIndex('idx_contribuyentes_activo');
        });

        Schema::table('cat_tipo_contribuyente', function (Blueprint $table) {
            $table->dropIndex('idx_tipo_contribuyente_area');
        });
    }
};
