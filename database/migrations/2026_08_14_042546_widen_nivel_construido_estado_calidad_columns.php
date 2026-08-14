<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_nivel_construido_predio_urbano', function (Blueprint $table) {
            $table->string('estado_construccion', 255)->change();
            $table->string('calidad_construccion', 255)->change();
        });
    }

    public function down(): void
    {
        Schema::table('tb_nivel_construido_predio_urbano', function (Blueprint $table) {
            $table->string('estado_construccion', 50)->change();
            $table->string('calidad_construccion', 50)->change();
        });
    }
};
