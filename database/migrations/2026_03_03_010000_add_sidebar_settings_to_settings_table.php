<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->unsignedSmallInteger('sidebar_font_size')->default(15)->after('app_font');
            $table->unsignedSmallInteger('sidebar_icon_size')->default(20)->after('sidebar_font_size');
            $table->unsignedSmallInteger('sidebar_width')->default(360)->after('sidebar_icon_size');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['sidebar_font_size', 'sidebar_icon_size', 'sidebar_width']);
        });
    }
};
