<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cat_colonia', function (Blueprint $table) {
            $table->string('codigo_postal', 5)->nullable()->after('COLONIA');
        });
    }

    public function down(): void
    {
        Schema::table('cat_colonia', function (Blueprint $table) {
            $table->dropColumn('codigo_postal');
        });
    }
};
