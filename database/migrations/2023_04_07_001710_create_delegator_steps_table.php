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
        Schema::create('delegator_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('delegator_id')->constrained();
            $table->string('step');
            $table->text('keterangan')->nullable();
            $table->foreignUuid('panitia_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delegator_steps');
    }
};
