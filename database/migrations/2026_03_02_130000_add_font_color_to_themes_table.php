<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('themes', function (Blueprint $table) {
            $table->string('font_color_dark', 20)->default('#bfdbfe')->after('glass_noise_texture');
            $table->string('font_color_light', 20)->default('#334155')->after('font_color_dark');
        });
    }

    public function down(): void
    {
        Schema::table('themes', function (Blueprint $table) {
            $table->dropColumn(['font_color_dark', 'font_color_light']);
        });
    }
};
