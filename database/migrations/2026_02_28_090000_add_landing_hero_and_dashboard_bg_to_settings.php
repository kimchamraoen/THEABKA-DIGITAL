<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Landing page hero customization
            $table->string('landing_hero_badge', 100)->nullable()->after('footer_text');
            $table->string('landing_hero_line1', 100)->nullable()->after('landing_hero_badge');
            $table->string('landing_hero_line2', 100)->nullable()->after('landing_hero_line1');
            $table->string('landing_hero_line3', 100)->nullable()->after('landing_hero_line2');
            $table->text('landing_hero_subtitle')->nullable()->after('landing_hero_line3');
            $table->string('landing_cta_primary_text', 50)->nullable()->after('landing_hero_subtitle');
            $table->string('landing_cta_primary_url', 500)->nullable()->after('landing_cta_primary_text');
            $table->string('landing_cta_secondary_text', 50)->nullable()->after('landing_cta_primary_url');
            $table->string('landing_cta_secondary_url', 500)->nullable()->after('landing_cta_secondary_text');

            // Features section customization
            $table->string('landing_features_title', 200)->nullable()->after('landing_cta_secondary_url');
            $table->string('landing_features_subtitle', 500)->nullable()->after('landing_features_title');
            $table->boolean('landing_features_visible')->default(true)->after('landing_features_subtitle');

            // CTA section customization
            $table->string('landing_cta_title', 200)->nullable()->after('landing_features_visible');
            $table->string('landing_cta_subtitle', 500)->nullable()->after('landing_cta_title');
            $table->boolean('landing_cta_visible')->default(true)->after('landing_cta_subtitle');

            // Floating cards toggle
            $table->boolean('landing_floating_cards')->default(true)->after('landing_cta_visible');
            $table->boolean('landing_particles')->default(true)->after('landing_floating_cards');

            // Dashboard / app background
            $table->string('app_bg_type', 20)->default('gradient')->after('landing_particles');
            $table->string('app_bg_image')->nullable()->after('app_bg_type');
            $table->string('app_bg_video')->nullable()->after('app_bg_image');

            // Auth background video file upload (replace URL)
            $table->string('auth_bg_video_file')->nullable()->after('auth_bg_video');

            // Landing background
            $table->string('landing_bg_type', 20)->default('gradient')->after('auth_bg_video_file');
            $table->string('landing_bg_image')->nullable()->after('landing_bg_type');
            $table->string('landing_bg_video')->nullable()->after('landing_bg_image');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'landing_hero_badge', 'landing_hero_line1', 'landing_hero_line2', 'landing_hero_line3',
                'landing_hero_subtitle', 'landing_cta_primary_text', 'landing_cta_primary_url',
                'landing_cta_secondary_text', 'landing_cta_secondary_url',
                'landing_features_title', 'landing_features_subtitle', 'landing_features_visible',
                'landing_cta_title', 'landing_cta_subtitle', 'landing_cta_visible',
                'landing_floating_cards', 'landing_particles',
                'app_bg_type', 'app_bg_image', 'app_bg_video',
                'auth_bg_video_file',
                'landing_bg_type', 'landing_bg_image', 'landing_bg_video',
            ]);
        });
    }
};
