<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('daftar_poli', function (Blueprint $table) {
            $table->date('tanggal')->nullable()->after('id_jadwal');
        });

        DB::table('daftar_poli')->whereNull('tanggal')->update(['tanggal' => DB::raw('DATE(created_at)')]);
    }

    public function down(): void
    {
        Schema::table('daftar_poli', function (Blueprint $table) {
            $table->dropColumn('tanggal');
        });
    }
};
