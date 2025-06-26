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
        Schema::table('participants', function (Blueprint $table) {
            $table->string('tempat_makesta')->nullable();
            $table->date('tanggal_makesta')->nullable();
            $table->string('no_surat_makesta')->nullable();
            $table->string('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn('tempat_makesta');
            $table->dropColumn('tanggal_makesta');
            $table->dropColumn('no_surat_makesta');
            $table->dropColumn('status');
        });
    }
};
