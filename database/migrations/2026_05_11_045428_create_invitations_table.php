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
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title_font_family')->nullable();
            $table->string('text_font_family')->nullable();
            $table->string('title_color')->nullable();
            $table->string('text_color')->nullable();
            $table->string('text_font_size')->nullable();
            $table->string('background_image')->nullable();
            $table->string('background_music')->nullable();
            $table->string('bride_name');
            $table->string('groom_name');
            $table->string('date')->nullable();
            $table->string('event_time')->nullable();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('address')->nullable();
            $table->string('title_invitation')->nullable();
            $table->string('message_invitation')->nullable();
            $table->string('title_thanks')->nullable();
            $table->string('message_thanks')->nullable();
            $table->string('link_map')->nullable();
            $table->string('map_photo')->nullable();
            $table->string('dollar_qr')->nullable();
            $table->string('khmer_qr')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('pre_wedding1')->nullable();
            $table->string('pre_wedding2')->nullable();
            $table->string('pre_wedding3')->nullable();
            $table->string('pre_wedding4')->nullable();
            $table->foreignId('template_id')->constrained()->onDelete('cascade');
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
