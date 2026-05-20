<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas_pagos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pago_id');
            $table->unsignedBigInteger('cuenta_id');
            $table->datetime('fecha_registro')->nullable();
            $table->integer('cantidad')->default(0);
            $table->decimal('monto', 10, 2)->default(0);
            $table->unsignedBigInteger('concepto_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas_pagos');
    }
};
