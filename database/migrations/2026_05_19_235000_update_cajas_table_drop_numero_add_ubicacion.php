<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('cajas', 'numero')) {
            Schema::table('cajas', function (Blueprint $table) {
                $table->dropColumn('numero');
            });
        }

        if (!Schema::hasColumn('cajas', 'ubicacion')) {
            Schema::table('cajas', function (Blueprint $table) {
                $table->string('ubicacion')->nullable()->after('nombre');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('cajas', 'ubicacion')) {
            Schema::table('cajas', function (Blueprint $table) {
                $table->dropColumn('ubicacion');
            });
        }

        if (!Schema::hasColumn('cajas', 'numero')) {
            Schema::table('cajas', function (Blueprint $table) {
                $table->string('numero')->after('nombre');
            });
        }
    }
};
