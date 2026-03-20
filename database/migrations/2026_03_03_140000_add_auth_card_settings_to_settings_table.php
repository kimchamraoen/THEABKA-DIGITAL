<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->integer('auth_card_max_width')->default(448)->after('auth_bg_video_file');
            $table->integer('auth_card_padding_x')->default(32)->after('auth_card_max_width');
            $table->integer('auth_card_padding_y')->default(24)->after('auth_card_padding_x');
            $table->integer('auth_card_border_radius')->default(16)->after('auth_card_padding_y');
            $table->integer('auth_card_font_size')->default(14)->after('auth_card_border_radius');
            $table->string('auth_card_font_color', 30)->nullable()->after('auth_card_font_size');
            $table->string('auth_label_color', 30)->nullable()->after('auth_card_font_color');
            $table->string('auth_heading_color', 30)->nullable()->after('auth_label_color');
            $table->string('auth_link_color', 30)->nullable()->after('auth_heading_color');
            $table->string('auth_btn_bg_color', 30)->nullable()->after('auth_link_color');
            $table->string('auth_btn_text_color', 30)->nullable()->after('auth_btn_bg_color');
            $table->integer('auth_logo_size')->default(48)->after('auth_btn_text_color');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'auth_card_max_width',
                'auth_card_padding_x',
                'auth_card_padding_y',
                'auth_card_border_radius',
                'auth_card_font_size',
                'auth_card_font_color',
                'auth_label_color',
                'auth_heading_color',
                'auth_link_color',
                'auth_btn_bg_color',
                'auth_btn_text_color',
                'auth_logo_size',
            ]);
        });
    }
};
