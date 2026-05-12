<?php

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
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('guest_name');
            $table->string('phone')->nullable();
            $table->string('group');
            $table->string('Greeting')->nullable();
            $table->string('note')->nullable();
            $table->string('gift_money')->nullable();
            $table->string('gift')->nullable();
            $table->string('statue');
            $table->uuid('uuid')->nullable()->unique();
            $table->foreignId('template_id')->nullable();
            $table->string('telegram_chat_id')->nullable();
            $table->string('telegram_account')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
