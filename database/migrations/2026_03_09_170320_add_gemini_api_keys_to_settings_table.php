<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->json('gemini_api_keys')->nullable()->after('gemini_api_key');
        });

        // Migrate existing single key to the array
        $settings = DB::table('settings')->first();
        if ($settings && !empty($settings->gemini_api_key)) {
            DB::table('settings')->where('id', $settings->id)->update([
                'gemini_api_keys' => json_encode([$settings->gemini_api_key]),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('gemini_api_keys');
        });
    }
};
