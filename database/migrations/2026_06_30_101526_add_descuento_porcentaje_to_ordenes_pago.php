<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('ordenes_pago', 'descuento_porcentaje')) {
            Schema::table('ordenes_pago', function (Blueprint $table) {
                $table->decimal('descuento_porcentaje', 5, 2)->nullable()->after('monto');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('ordenes_pago', 'descuento_porcentaje')) {
            Schema::table('ordenes_pago', function (Blueprint $table) {
                $table->dropColumn('descuento_porcentaje');
            });
        }
    }
};
