<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('descuentos', function (Blueprint $table) {
            $table->id();
            $table->string('idPredio');
            $table->foreignId('idUser')->constrained('users')->onDelete('cascade');
            $table->decimal('multas', 5, 2)->default(0);
            $table->decimal('actualizaciones', 5, 2)->default(0);
            $table->decimal('gastos_cobranza', 5, 2)->default(0);
            $table->date('fecha_expiracion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('descuentos');
    }
};
