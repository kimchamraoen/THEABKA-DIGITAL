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
        Schema::table('templates', function (Blueprint $table) {
            $table->string('video_url')->nullable();
            $table->string('video_public_id')->nullable();
            $table->string('link_dollar')->nullable();
            $table->string('link_khmer')->nullable();
            $table->string('event')->nullable();
            $table->string('option')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn(['video_url', 'video_public_id', 'link_dollar', 'link_khmer', 'event', 'option']);
        });
    }
};
