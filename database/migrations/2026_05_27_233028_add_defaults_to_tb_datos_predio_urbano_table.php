<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE tb_datos_predio_urbano MODIFY id_zona_urbana TINYINT UNSIGNED NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE tb_datos_predio_urbano MODIFY superficie_terreno_metros_cuadrados DECIMAL(10,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE tb_datos_predio_urbano MODIFY Frente_metros DECIMAL(8,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE tb_datos_predio_urbano MODIFY Fondo_metros DECIMAL(8,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE tb_datos_predio_urbano MODIFY Baldio TINYINT(1) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE tb_datos_predio_urbano MODIFY id_forma_predio TINYINT UNSIGNED NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE tb_datos_predio_urbano MODIFY id_uso_predio TINYINT UNSIGNED NOT NULL DEFAULT 1');
        DB::statement('ALTER TABLE tb_datos_predio_urbano MODIFY id_pavimientacion TINYINT UNSIGNED NOT NULL DEFAULT 7');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE tb_datos_predio_urbano MODIFY id_zona_urbana TINYINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE tb_datos_predio_urbano MODIFY superficie_terreno_metros_cuadrados DECIMAL(10,2) NOT NULL');
        DB::statement('ALTER TABLE tb_datos_predio_urbano MODIFY Frente_metros DECIMAL(8,2) NOT NULL');
        DB::statement('ALTER TABLE tb_datos_predio_urbano MODIFY Fondo_metros DECIMAL(8,2) NOT NULL');
        DB::statement('ALTER TABLE tb_datos_predio_urbano MODIFY Baldio TINYINT(1) NOT NULL');
        DB::statement('ALTER TABLE tb_datos_predio_urbano MODIFY id_forma_predio TINYINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE tb_datos_predio_urbano MODIFY id_uso_predio TINYINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE tb_datos_predio_urbano MODIFY id_pavimientacion TINYINT UNSIGNED NOT NULL');
    }
};
