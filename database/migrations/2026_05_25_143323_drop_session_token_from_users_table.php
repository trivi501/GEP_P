<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('users', 'session_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('session_token');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('users', 'session_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('session_token')->nullable()->after('secretaria_id');
            });
        }
    }
};
