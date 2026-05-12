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
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();

            $table->string('title_font_family')->default('Arial');
            $table->string('text_font_family')->default('Arial');

            $table->string('title_color')->default('#f9af59');
            $table->string('text_color')->default('#f9af59');

            $table->string('text_font_size')->default('18px');

            $table->json('background_images')->nullable();
            $table->json('background_music')->nullable();

            $table->string('bride_name')->nullable();
            $table->string('groom_name')->nullable();

            $table->string('date')->nullable();
            $table->string('event_time')->nullable();

            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();

            $table->string('address')->nullable();

            $table->string('title_invitation')->nullable();
            $table->text('message_invitation')->nullable();

            $table->string('title_thanks')->nullable();
            $table->text('message_thanks')->nullable();

            $table->string('link_map')->nullable();
            $table->string('map_photo')->nullable();

            $table->string('dollar_qr')->nullable();
            $table->string('khmer_qr')->nullable();

            $table->string('pre_wedding1')->nullable();
            $table->string('pre_wedding2')->nullable();
            $table->string('pre_wedding3')->nullable();
            $table->string('pre_wedding4')->nullable();

            $table->string('cover_image')->nullable();
            $table->string('type');
            $table->json('data')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
