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
        Schema::create('delegators', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->enum('tingkat', ['pac', 'pr', 'pk']);
            $table->enum('banom', ['ipnu', 'ippnu']);
            $table->string('address_code');
            $table->string('whatsapp');

            $table->string('surat_pengesahan');
            $table->string('surat_tugas');
            
            $table->foreignUuid('payment_id')->nullable();
            $table->unsignedInteger('attempt')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delegators');
    }
};
