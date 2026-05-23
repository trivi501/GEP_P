<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas_por_secretaria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('secretaria_id')->constrained('secretarias')->cascadeOnDelete();
            $table->foreignId('cuenta_id')->constrained('cuentas')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['secretaria_id', 'cuenta_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas_por_secretaria');
    }
};
