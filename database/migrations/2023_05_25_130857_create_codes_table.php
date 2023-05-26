<?php

use App\Models\Delegator;
use App\Models\Participant;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('codes', function (Blueprint $table) {
            $table->string('id', 8)->primary();
            $table->uuidMorphs('content');
            $table->timestamps();
            $table->softDeletes();
        });

        //Display Message
        $output = new ConsoleOutput();
        $output->writeln('');
        $progress = new ProgressBar($output, 4);

        DB::transaction(function() use($progress) {
            $progress->advance();
            User::generateMissingsCode();
            $progress->advance();
            Delegator::generateMissingsCode();
            $progress->advance();
            Participant::generateMissingsCode();
            $progress->advance();
            Payment::generateMissingsCode();
        });

        $progress->finish();
        $progress->clear();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codes');
    }
};
