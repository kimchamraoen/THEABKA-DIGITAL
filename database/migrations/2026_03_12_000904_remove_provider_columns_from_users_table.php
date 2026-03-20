<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Before dropping columns, migrate existing social data into social_accounts table.
     */
    public function up(): void
    {
        // Migrate existing provider data to social_accounts
        $users = DB::table('users')
            ->whereNotNull('provider')
            ->whereNotNull('provider_id')
            ->get(['id', 'provider', 'provider_id', 'avatar']);

        foreach ($users as $user) {
            DB::table('social_accounts')->insertOrIgnore([
                'user_id' => $user->id,
                'provider' => $user->provider,
                'provider_id' => $user->provider_id,
                'avatar' => $user->avatar,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['provider', 'provider_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('provider')->nullable()->after('password');
            $table->string('provider_id')->nullable()->after('provider');
        });
    }
};
