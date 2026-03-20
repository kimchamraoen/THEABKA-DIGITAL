<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('font_type_en', 20)->nullable()->after('app_font');
            $table->string('font_value_en', 255)->nullable()->after('font_type_en');
            $table->string('font_type_km', 20)->nullable()->after('font_value_en');
            $table->string('font_value_km', 255)->nullable()->after('font_type_km');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'font_type_en',
                'font_value_en',
                'font_type_km',
                'font_value_km',
            ]);
        });
    }
};
