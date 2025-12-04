<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mood_entries', function (Blueprint $table) {
            $table->enum('time_of_day', ['morning', 'afternoon', 'evening'])->after('mood_score');
        });
    }

    public function down(): void
    {
        Schema::table('mood_entries', function (Blueprint $table) {
            $table->dropColumn('time_of_day');
        });
    }
};
