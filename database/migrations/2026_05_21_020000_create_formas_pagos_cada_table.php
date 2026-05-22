<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formas_pagos_cada', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pago_id');
            $table->integer('forma_pago_id');
            $table->decimal('monto', 10, 2);

            $table->foreign('pago_id')->references('id')->on('pagos')->onDelete('cascade');
            $table->foreign('forma_pago_id')->references('id')->on('f4_c_formapago');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formas_pagos_cada');
    }
};
