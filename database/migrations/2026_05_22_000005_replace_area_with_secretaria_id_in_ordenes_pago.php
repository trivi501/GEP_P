<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordenes_pago', function (Blueprint $table) {
            $table->dropColumn('area');
            $table->foreignId('secretaria_id')->nullable()->constrained('secretarias')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ordenes_pago', function (Blueprint $table) {
            $table->dropForeign(['secretaria_id']);
            $table->dropColumn('secretaria_id');
            $table->string('area')->nullable();
        });
    }
};
