<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('descuentos', 'aseo_publico')) {
            Schema::table('descuentos', function (Blueprint $table) {
                $table->decimal('aseo_publico', 5, 2)->default(0)->after('recargos');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('descuentos', 'aseo_publico')) {
            Schema::table('descuentos', function (Blueprint $table) {
                $table->dropColumn('aseo_publico');
            });
        }
    }
};
