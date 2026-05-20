<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_caja', function (Blueprint $table) {
            $table->id();
            $table->decimal('fondo', 10, 2)->default(0);
            $table->decimal('total_ingreso', 10, 2)->default(0);
            $table->datetime('datetime_apertura')->nullable();
            $table->datetime('datetime_cierre')->nullable();
            $table->unsignedBigInteger('cajero_id');
            $table->unsignedBigInteger('caja_id');
            $table->unsignedBigInteger('cortecaja_id')->nullable();

            $table->foreign('cajero_id')->references('id_cajero')->on('cajeros')->onDelete('cascade');
            $table->foreign('caja_id')->references('id')->on('cajas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_caja');
    }
};
