<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'admin' to the role enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'admin', 'user') NOT NULL DEFAULT 'user'");

        // Broadcast notifications table
        Schema::create('broadcast_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->string('target_role'); // 'user', 'admin', 'all'
            $table->timestamps();
        });

        // Pivot table for read tracking
        Schema::create('broadcast_notification_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('broadcast_notification_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->unique(['broadcast_notification_id', 'user_id'], 'bn_user_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_notification_user');
        Schema::dropIfExists('broadcast_notifications');
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'user') NOT NULL DEFAULT 'user'");
    }
};
