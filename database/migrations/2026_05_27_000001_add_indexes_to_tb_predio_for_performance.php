<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_predio', function (Blueprint $table) {
            $table->index('ubicacion', 'idx_predio_ubicacion');
            $table->index('año_ultimo_pago', 'idx_predio_anio_ultimo_pago');
            $table->index('ultimo_bimestre_pago', 'idx_predio_ultimo_bimestre');
            $table->index('superficie', 'idx_predio_superficie');
            $table->index('construccion', 'idx_predio_construccion');
        });

        if (Schema::hasTable('cat_colonia')) {
            Schema::table('cat_colonia', function (Blueprint $table) {
                $table->index('COLONIA', 'idx_colonia_nombre');
            });
        }

        if (Schema::hasTable('cat_tipo_predio')) {
            Schema::table('cat_tipo_predio', function (Blueprint $table) {
                $table->index('Tipo_predio', 'idx_tipo_predio_nombre');
            });
        }

        if (Schema::hasTable('cat_estado_impuesto')) {
            Schema::table('cat_estado_impuesto', function (Blueprint $table) {
                $table->index('DESCRIPCION', 'idx_estado_impuesto_descripcion');
            });
        }
    }

    public function down(): void
    {
        Schema::table('tb_predio', function (Blueprint $table) {
            $table->dropIndex('idx_predio_ubicacion');
            $table->dropIndex('idx_predio_anio_ultimo_pago');
            $table->dropIndex('idx_predio_ultimo_bimestre');
            $table->dropIndex('idx_predio_superficie');
            $table->dropIndex('idx_predio_construccion');
        });

        if (Schema::hasTable('cat_colonia')) {
            Schema::table('cat_colonia', function (Blueprint $table) {
                $table->dropIndex('idx_colonia_nombre');
            });
        }

        if (Schema::hasTable('cat_tipo_predio')) {
            Schema::table('cat_tipo_predio', function (Blueprint $table) {
                $table->dropIndex('idx_tipo_predio_nombre');
            });
        }

        if (Schema::hasTable('cat_estado_impuesto')) {
            Schema::table('cat_estado_impuesto', function (Blueprint $table) {
                $table->dropIndex('idx_estado_impuesto_descripcion');
            });
        }
    }
};
