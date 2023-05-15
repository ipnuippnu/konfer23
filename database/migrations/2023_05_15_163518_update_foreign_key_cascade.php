<?php

use App\Models\Delegator;
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
        Schema::table('payments', function(Blueprint $table){
            $table->dropForeignIdFor(Delegator::class, 'owner_id');
            $table->foreign('owner_id')->references('id')->on('delegators')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('participants', function(Blueprint $table){
            $table->dropForeignIdFor(Delegator::class, 'delegator_id');
            $table->foreign('delegator_id')->references('id')->on('delegators')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('delegator_steps', function(Blueprint $table){
            $table->dropForeignIdFor(Delegator::class, 'delegator_id');
            $table->foreign('delegator_id')->references('id')->on('delegators')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('payments', function(Blueprint $table){
            $table->dropForeignIdFor(Delegator::class, 'owner_id');
            $table->foreign('owner_id')->references('id')->on('delegators');
        });

        Schema::table('participants', function(Blueprint $table){
            $table->dropForeignIdFor(Delegator::class, 'delegator_id');
            $table->foreign('delegator_id')->references('id')->on('delegators');
        });

        Schema::table('delegator_steps', function(Blueprint $table){
            $table->dropForeignIdFor(Delegator::class, 'delegator_id');
            $table->foreign('delegator_id')->references('id')->on('delegators');
        });
    }
};
