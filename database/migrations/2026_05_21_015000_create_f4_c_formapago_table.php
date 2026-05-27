<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('f4_c_formapago')) {
            return;
        }
        Schema::create('f4_c_formapago', function (Blueprint $table) {
            $table->id();
            $table->string('c_FormaPago')->nullable();
            $table->string('Descripción')->nullable();
            $table->string('Bancarizado')->nullable();
            $table->string('Número de operación')->nullable();
            $table->string('RFC del Emisor de la cuenta ordenante')->nullable();
            $table->string('Cuenta Ordenante')->nullable();
            $table->string('Patrón para cuenta ordenante')->nullable();
            $table->string('RFC del Emisor Cuenta de Beneficiario')->nullable();
            $table->string('Cuenta de Benenficiario')->nullable();
            $table->string('Patrón para cuenta Beneficiaria')->nullable();
            $table->string('Tipo Cadena Pago')->nullable();
            $table->string('Nombre del Banco emisor de la cuenta ordenante en caso de extran')->nullable();
            $table->boolean('activo')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('f4_c_formapago');
    }
};
