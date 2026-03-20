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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('terms_accepted')->default(false)->after('bypass_email_verification');
            $table->timestamp('terms_accepted_at')->nullable()->after('terms_accepted');
            $table->boolean('privacy_accepted')->default(false)->after('terms_accepted_at');
            $table->timestamp('privacy_accepted_at')->nullable()->after('privacy_accepted');
            $table->enum('cookie_consent', ['accepted', 'declined', 'pending'])->default('pending')->after('privacy_accepted_at');
            $table->timestamp('cookie_consent_at')->nullable()->after('cookie_consent');
            $table->string('agreement_ip')->nullable()->after('cookie_consent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'terms_accepted',
                'terms_accepted_at',
                'privacy_accepted',
                'privacy_accepted_at',
                'cookie_consent',
                'cookie_consent_at',
                'agreement_ip',
            ]);
        });
    }
};
