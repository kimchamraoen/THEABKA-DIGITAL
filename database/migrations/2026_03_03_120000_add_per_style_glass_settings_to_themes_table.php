<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-glass-style settings stored as JSON on the themes table.
     * Each key (liquid, card, crystal, frosted, glass3d) stores its own
     * blur, bg_opacity, border_opacity, shadow_opacity, saturation,
     * brightness, tint_color, noise_texture values.
     */
    public function up(): void
    {
        Schema::table('themes', function (Blueprint $table) {
            $table->json('glass_style_settings')->nullable()->after('glass_noise_texture');
        });
    }

    public function down(): void
    {
        Schema::table('themes', function (Blueprint $table) {
            $table->dropColumn('glass_style_settings');
        });
    }
};
