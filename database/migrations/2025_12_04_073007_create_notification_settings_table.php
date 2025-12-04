<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->time('morning_time')->default('09:00');
            $table->time('afternoon_time')->default('14:00');
            $table->time('evening_time')->default('20:00');
            $table->boolean('morning_enabled')->default(true);
            $table->boolean('afternoon_enabled')->default(true);
            $table->boolean('evening_enabled')->default(true);
            $table->string('timezone')->default('Europe/Kyiv');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
