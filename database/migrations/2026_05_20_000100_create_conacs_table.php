<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conac', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamp('created')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conac');
    }
};
