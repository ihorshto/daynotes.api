<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notification_settings', function (Blueprint $table) {
            $table->boolean('email_enabled')->after('timezone');
            $table->boolean('telegram_enabled')->after('email_enabled');
            $table->string('telegram_chat_id')->nullable()->after('telegram_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('notification_settings', function (Blueprint $table) {
            $table->dropColumn(['email_enabled', 'telegram_enabled', 'telegram_chat_id']);
        });
    }
};
