<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('cuentas_pagos', 'comcepto_id')) {
            Schema::table('cuentas_pagos', function (Blueprint $table) {
                $table->renameColumn('comcepto_id', 'concepto_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('cuentas_pagos', 'concepto_id')) {
            Schema::table('cuentas_pagos', function (Blueprint $table) {
                $table->renameColumn('concepto_id', 'comcepto_id');
            });
        }
    }
};
