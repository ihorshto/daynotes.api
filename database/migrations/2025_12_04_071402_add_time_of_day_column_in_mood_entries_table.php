<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mood_entries', function (Blueprint $table): void {
            $table->enum('time_of_day', ['morning', 'afternoon', 'evening'])->after('mood_score');
        });
    }

    public function down(): void
    {
        Schema::table('mood_entries', function (Blueprint $table): void {
            $table->dropColumn('time_of_day');
        });
    }
};
