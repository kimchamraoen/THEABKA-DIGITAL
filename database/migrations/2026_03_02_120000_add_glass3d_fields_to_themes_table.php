<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('themes', function (Blueprint $table) {
            $table->string('glass_brightness', 10)->default('0.85')->after('glass_saturation');
            $table->string('glass_noise_texture', 40)->default('egg-shell')->after('glass_brightness');
        });
    }

    public function down(): void
    {
        Schema::table('themes', function (Blueprint $table) {
            $table->dropColumn(['glass_brightness', 'glass_noise_texture']);
        });
    }
};
