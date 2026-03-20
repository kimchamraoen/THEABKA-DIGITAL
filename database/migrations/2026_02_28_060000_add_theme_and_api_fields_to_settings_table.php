<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->text('google_fonts_api_key')->nullable()->after('app_font');
            $table->text('custom_css')->nullable()->after('force_2fa');

            // Custom theme color palette
            $table->string('color_primary', 30)->default('#3b82f6')->after('force_2fa');
            $table->string('color_secondary', 30)->default('#6366f1')->after('color_primary');
            $table->string('color_accent', 30)->default('#8b5cf6')->after('color_secondary');
            $table->string('color_success', 30)->default('#22c55e')->after('color_accent');
            $table->string('color_warning', 30)->default('#eab308')->after('color_success');
            $table->string('color_danger', 30)->default('#ef4444')->after('color_warning');

            // Dark/Light background gradients
            $table->string('dark_bg_from', 30)->default('#172554')->after('color_danger');
            $table->string('dark_bg_via', 30)->default('#1e3a5f')->after('dark_bg_from');
            $table->string('dark_bg_to', 30)->default('#0f172a')->after('dark_bg_via');
            $table->string('light_bg_from', 30)->default('#f3f4f6')->after('dark_bg_to');
            $table->string('light_bg_via', 30)->default('#eff6ff')->after('light_bg_from');
            $table->string('light_bg_to', 30)->default('#e5e7eb')->after('light_bg_via');

            // Glass card styling
            $table->string('glass_blur', 10)->default('20px')->after('light_bg_to');
            $table->string('glass_opacity', 10)->default('0.1')->after('glass_blur');
            $table->string('glass_border_opacity', 10)->default('0.2')->after('glass_opacity');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'google_fonts_api_key',
                'custom_css',
                'color_primary', 'color_secondary', 'color_accent',
                'color_success', 'color_warning', 'color_danger',
                'dark_bg_from', 'dark_bg_via', 'dark_bg_to',
                'light_bg_from', 'light_bg_via', 'light_bg_to',
                'glass_blur', 'glass_opacity', 'glass_border_opacity',
            ]);
        });
    }
};
