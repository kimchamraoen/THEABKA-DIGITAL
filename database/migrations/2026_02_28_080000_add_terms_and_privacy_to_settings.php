<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->longText('terms_content')->nullable()->after('welcome_email_text');
            $table->longText('privacy_content')->nullable()->after('terms_content');
            $table->string('footer_text')->nullable()->after('privacy_content');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['terms_content', 'privacy_content', 'footer_text']);
        });
    }
};
