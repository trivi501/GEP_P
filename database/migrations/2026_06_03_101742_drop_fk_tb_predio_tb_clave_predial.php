<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $fkExists = collect(\Illuminate\Support\Facades\DB::select(
            "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?",
            [env('DB_DATABASE'), 'tb_predio', 'FK_tb_predio_tb_clave_predial']
        ))->isNotEmpty();

        if ($fkExists) {
            Schema::table('tb_predio', function (Blueprint $table) {
                $table->dropForeign('FK_tb_predio_tb_clave_predial');
            });
        }
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
