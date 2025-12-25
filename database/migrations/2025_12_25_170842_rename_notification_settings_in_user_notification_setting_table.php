<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('notification_settings')) {
            Schema::rename('notification_settings', 'user_notification_settings');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_notification_settings')) {
            Schema::rename('user_notification_settings', 'notification_settings');
        }
    }
};
