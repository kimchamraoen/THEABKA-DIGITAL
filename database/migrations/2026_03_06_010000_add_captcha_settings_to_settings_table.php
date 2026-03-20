<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('captcha_provider')->nullable()->default(null)->after('force_2fa');
            $table->string('recaptcha_site_key')->nullable()->after('captcha_provider');
            $table->string('recaptcha_secret_key')->nullable()->after('recaptcha_site_key');
            $table->string('turnstile_site_key')->nullable()->after('recaptcha_secret_key');
            $table->string('turnstile_secret_key')->nullable()->after('turnstile_site_key');
            $table->boolean('captcha_on_login')->default(true)->after('turnstile_secret_key');
            $table->boolean('captcha_on_register')->default(true)->after('captcha_on_login');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'captcha_provider',
                'recaptcha_site_key',
                'recaptcha_secret_key',
                'turnstile_site_key',
                'turnstile_secret_key',
                'captcha_on_login',
                'captcha_on_register',
            ]);
        });
    }
};
