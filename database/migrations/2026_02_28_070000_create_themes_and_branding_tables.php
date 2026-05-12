<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Named themes that users can select
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);

            // Background type: gradient, image, video
            $table->string('bg_type')->default('gradient'); // gradient | image | video
            $table->string('bg_image')->nullable(); // path to uploaded image
            $table->string('bg_video')->nullable(); // path to uploaded video or URL
            $table->string('bg_overlay_opacity')->default('0.5');
            $table->string('bg_overlay_color')->default('#000000');

            // Gradient colors (dark mode)
            $table->string('dark_bg_from', 30)->default('#0f0c29');
            $table->string('dark_bg_via', 30)->default('#302b63');
            $table->string('dark_bg_to', 30)->default('#24243e');
            $table->string('dark_gradient_direction')->default('135deg');

            // Gradient colors (light mode)
            $table->string('light_bg_from', 30)->default('#e0eafc');
            $table->string('light_bg_via', 30)->default('#cfdef3');
            $table->string('light_bg_to', 30)->default('#e2d1c3');
            $table->string('light_gradient_direction')->default('135deg');

            // Color palette
            $table->string('color_primary', 30)->default('#3b82f6');
            $table->string('color_secondary', 30)->default('#6366f1');
            $table->string('color_accent', 30)->default('#8b5cf6');
            $table->string('color_success', 30)->default('#22c55e');
            $table->string('color_warning', 30)->default('#eab308');
            $table->string('color_danger', 30)->default('#ef4444');

            // Glass morphism settings
            $table->string('glass_blur', 10)->default('20px');
            $table->string('glass_bg_opacity', 10)->default('0.08');
            $table->string('glass_border_opacity', 10)->default('0.15');
            $table->string('glass_shadow_opacity', 10)->default('0.25');

            // Blob decoration colors
            $table->string('blob_color_1', 30)->default('#8b5cf6');
            $table->string('blob_color_2', 30)->default('#ec4899');
            $table->string('blob_color_3', 30)->default('#3b82f6');
            $table->string('blob_color_4', 30)->default('#f97316');
            $table->boolean('blobs_enabled')->default(true);

            // Custom CSS
            $table->text('custom_css')->nullable();

            $table->timestamps();
        });

        // Landing page sections
        Schema::create('landing_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_key')->unique(); // hero, features, cta, etc.
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('body')->nullable();
            $table->string('image')->nullable();
            $table->string('video_url')->nullable(); // YouTube embed URL
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });

        // Add branding & email fields to settings
        Schema::table('settings', function (Blueprint $table) {
            $table->string('app_name')->default('G2FA')->after('id');
            $table->string('app_logo')->nullable()->after('app_name'); // path to uploaded logo
            $table->string('app_favicon')->nullable()->after('app_logo');
            $table->unsignedBigInteger('active_theme_id')->nullable()->after('app_favicon');
            $table->string('auth_bg_type')->default('gradient')->after('glass_border_opacity'); // gradient | image | video
            $table->string('auth_bg_image')->nullable()->after('auth_bg_type');
            $table->string('auth_bg_video')->nullable()->after('auth_bg_image');

            // Email template texts
            $table->text('verify_email_text')->nullable()->after('auth_bg_video');
            $table->text('forgot_password_text')->nullable()->after('verify_email_text');
            $table->text('welcome_email_text')->nullable()->after('forgot_password_text');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_sections');
        Schema::dropIfExists('themes');

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'app_name', 'app_logo', 'app_favicon', 'active_theme_id',
                'auth_bg_type', 'auth_bg_image', 'auth_bg_video',
                'verify_email_text', 'forgot_password_text', 'welcome_email_text',
            ]);
        });
    }
};
