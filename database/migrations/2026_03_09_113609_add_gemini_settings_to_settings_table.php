<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('gemini_api_key', 500)->nullable()->after('google_fonts_api_key');
            $table->string('translation_source_language', 10)->default('en')->after('gemini_api_key');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['gemini_api_key', 'translation_source_language']);
        });
    }
};
