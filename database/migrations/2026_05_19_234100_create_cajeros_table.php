<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cajeros', function (Blueprint $table) {
            $table->id('id_cajero');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('caja_id');
            $table->tinyInteger('status')->default(1);
            $table->timestamp('created')->useCurrent();

            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('caja_id')->references('id')->on('cajas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cajeros');
    }
};
