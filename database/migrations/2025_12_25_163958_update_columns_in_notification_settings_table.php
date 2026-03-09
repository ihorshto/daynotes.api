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
        Schema::table('notification_settings', function (Blueprint $table): void {
            // Delete columns
            if (Schema::hasColumn('notification_settings', 'morning_time')) {
                $table->dropColumn('morning_time');
            }

            if (Schema::hasColumn('notification_settings', 'afternoon_time')) {
                $table->dropColumn('afternoon_time');
            }

            if (Schema::hasColumn('notification_settings', 'evening_time')) {
                $table->dropColumn('evening_time');
            }

            if (Schema::hasColumn('notification_settings', 'morning_enabled')) {
                $table->dropColumn('morning_enabled');
            }

            if (Schema::hasColumn('notification_settings', 'afternoon_enabled')) {
                $table->dropColumn('afternoon_enabled');
            }

            if (Schema::hasColumn('notification_settings', 'evening_enabled')) {
                $table->dropColumn('evening_enabled');
            }

            if (Schema::hasColumn('notification_settings', 'telegram_chat_id')) {
                $table->dropColumn('telegram_chat_id');
            }

            // Add new 'time' column
            if (! Schema::hasColumn('notification_settings', 'time')) {
                $table->time('time')->after('user_id')->default('09:00');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_settings', function (Blueprint $table): void {
            // Re-add deleted columns
            if (! Schema::hasColumn('notification_settings', 'morning_time')) {
                $table->time('morning_time')->default('09:00');
            }

            if (! Schema::hasColumn('notification_settings', 'afternoon_time')) {
                $table->time('afternoon_time')->default('13:00');
            }

            if (! Schema::hasColumn('notification_settings', 'evening_time')) {
                $table->time('evening_time')->default('19:00');
            }

            if (! Schema::hasColumn('notification_settings', 'morning_enabled')) {
                $table->boolean('morning_enabled')->default(true);
            }

            if (! Schema::hasColumn('notification_settings', 'afternoon_enabled')) {
                $table->boolean('afternoon_enabled')->default(true);
            }

            if (! Schema::hasColumn('notification_settings', 'evening_enabled')) {
                $table->boolean('evening_enabled')->default(true);
            }

            if (! Schema::hasColumn('notification_settings', 'telegram_chat_id')) {
                $table->string('telegram_chat_id')->nullable();
            }

            // Drop the 'time' column
            if (Schema::hasColumn('notification_settings', 'time')) {
                $table->dropColumn('time');
            }
        });
    }
};
