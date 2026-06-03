<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_predio', function (Blueprint $table) {
            $table->dropForeign('FK_tb_predio_tb_clave_predial');
        });
    }

    public function down(): void
    {
        Schema::table('tb_predio', function (Blueprint $table) {
            $table->foreign('id_clave_predial', 'FK_tb_predio_tb_clave_predial')
                ->references('id_clave_predial')
                ->on('tb_clave_predial');
        });
    }
};
