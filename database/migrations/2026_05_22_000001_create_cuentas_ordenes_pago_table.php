<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas_ordenes_pago', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_pago_id')->constrained('ordenes_pago')->cascadeOnDelete();
            $table->foreignId('IdCuenta')->constrained('cuentas', 'id')->cascadeOnDelete();
            $table->decimal('monto', 12, 2)->default(0);
            $table->decimal('cantidad', 10, 2)->default(1);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->timestamp('created')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas_ordenes_pago');
    }
};
