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
        // Add global setting for allowing unverified login
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('allow_unverified_login')->default(false)->after('force_2fa');
        });

        // Add per-user setting for bypassing email verification
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('bypass_email_verification')->default(false)->after('email_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('allow_unverified_login');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('bypass_email_verification');
        });
    }
};
