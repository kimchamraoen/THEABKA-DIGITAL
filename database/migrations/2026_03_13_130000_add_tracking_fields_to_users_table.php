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
            $table->string('login_provider')->nullable()->after('avatar_provider');
            $table->timestamp('last_login_at')->nullable()->after('login_provider');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            $table->string('last_login_device')->nullable()->after('last_login_ip');
            $table->string('last_login_browser')->nullable()->after('last_login_device');
            $table->string('last_login_os')->nullable()->after('last_login_browser');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'login_provider',
                'last_login_at',
                'last_login_ip',
                'last_login_device',
                'last_login_browser',
                'last_login_os',
            ]);
        });
    }
};
