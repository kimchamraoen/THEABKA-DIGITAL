<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('themes', function (Blueprint $table) {
            $table->string('glass_tint_color', 30)->default('#ffffff')->after('glass_shadow_opacity');
            $table->string('glass_saturation', 10)->default('1.8')->after('glass_tint_color');
            $table->string('glass_noise_opacity', 10)->default('0.03')->after('glass_saturation');
        });
    }

    public function down(): void
    {
        Schema::table('themes', function (Blueprint $table) {
            $table->dropColumn(['glass_tint_color', 'glass_saturation', 'glass_noise_opacity']);
        });
    }
};
