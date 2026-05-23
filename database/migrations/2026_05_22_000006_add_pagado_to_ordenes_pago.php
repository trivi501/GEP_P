<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordenes_pago', function (Blueprint $table) {
            $table->boolean('pagado')->default(false)->after('monto');
            $table->timestamp('fecha_pago')->nullable()->after('pagado');
        });
    }

    public function down(): void
    {
        Schema::table('ordenes_pago', function (Blueprint $table) {
            $table->dropColumn(['pagado', 'fecha_pago']);
        });
    }
};
