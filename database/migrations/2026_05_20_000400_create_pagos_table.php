<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->decimal('monto', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->string('folio');
            $table->datetime('fecha')->nullable();
            $table->string('estatus');
            $table->string('forma_pago');
            $table->string('tipo_pago');
            $table->string('nombre');
            $table->string('rfc');
            $table->text('descripcion')->nullable();
            $table->string('id_predio');
            $table->string('id_contribuyente');
            $table->unsignedBigInteger('id_historial_caja');
            $table->unsignedBigInteger('id_usuario');
            $table->year('anio_pago');
            $table->string('im', 50)->nullable();
            $table->string('url_file')->nullable();

            $table->foreign('id_historial_caja')->references('id')->on('historial_caja')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
