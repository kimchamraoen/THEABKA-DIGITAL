<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->text('custom_css_landing')->nullable()->after('custom_css');
            $table->boolean('custom_css_landing_enabled')->default(false)->after('custom_css_landing');
            $table->text('custom_css_dashboard')->nullable()->after('custom_css_landing_enabled');
            $table->boolean('custom_css_dashboard_enabled')->default(false)->after('custom_css_dashboard');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'custom_css_landing',
                'custom_css_landing_enabled',
                'custom_css_dashboard',
                'custom_css_dashboard_enabled',
            ]);
        });
    }
};
