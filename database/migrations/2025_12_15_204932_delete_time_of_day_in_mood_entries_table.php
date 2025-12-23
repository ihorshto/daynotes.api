<?php

declare(strict_types=1);

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
        Schema::table('mood_entries', function (Blueprint $table): void {
            if (Schema::hasColumn('mood_entries', 'time_of_day')) {
                $table->dropColumn('time_of_day');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mood_entries', function (Blueprint $table): void {
            if (! Schema::hasColumn('mood_entries', 'time_of_day')) {
                $table->string('time_of_day')->nullable();

            }
        });
    }
};
