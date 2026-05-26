<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corte_cajas', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->decimal('ingresos', 10, 2)->default(0);
            $table->decimal('urbano', 10, 2)->default(0);
            $table->decimal('rustico', 10, 2)->default(0);
            $table->integer('recibos_efectivos')->default(0);
            $table->integer('recibos_cancelados')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corte_cajas');
    }
};
