<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_predio', function (Blueprint $table) {
            $table->decimal('valor_catastral', 18, 2)->nullable()->change();
            $table->decimal('valor_fiscal', 18, 2)->nullable()->change();
        });

        Schema::table('tb_datos_predio_urbano', function (Blueprint $table) {
            $table->decimal('valor_catastral_terreno', 18, 2)->nullable()->change();
            $table->decimal('valor_catastral_construido', 18, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tb_predio', function (Blueprint $table) {
            $table->decimal('valor_catastral', 10, 2)->nullable()->change();
            $table->decimal('valor_fiscal', 10, 2)->nullable()->change();
        });

        Schema::table('tb_datos_predio_urbano', function (Blueprint $table) {
            $table->decimal('valor_catastral_terreno', 10, 2)->nullable()->change();
            $table->decimal('valor_catastral_construido', 10, 2)->nullable()->change();
        });
    }
};
