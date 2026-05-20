<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas', function (Blueprint $table) {
            $table->id();
            $table->string('indetec')->nullable();
            $table->string('nom_indetect')->nullable();
            $table->string('cuenta')->nullable();
            $table->string('subcuenta')->nullable();
            $table->text('descripcion')->nullable();
            $table->decimal('importe', 10, 2)->default(0);
            $table->unsignedBigInteger('cuentaMayor_id')->nullable();
            $table->unsignedBigInteger('indetecMayor_id')->nullable();
            $table->unsignedBigInteger('conac_id')->nullable();
            $table->timestamps();

            $table->foreign('conac_id')->references('id')->on('conac')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas');
    }
};
