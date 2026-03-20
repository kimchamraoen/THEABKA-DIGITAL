<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('sidebar_active_bg_color', 50)->default('rgba(255,255,255,0.15)')->after('sidebar_collapsed_width');
            $table->string('sidebar_active_border_color', 50)->default('rgba(255,255,255,0.2)')->after('sidebar_active_bg_color');
            $table->unsignedTinyInteger('sidebar_active_border_radius')->default(12)->after('sidebar_active_border_color');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['sidebar_active_bg_color', 'sidebar_active_border_color', 'sidebar_active_border_radius']);
        });
    }
};
