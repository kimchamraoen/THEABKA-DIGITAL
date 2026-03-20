<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('footer_sticky')->default(true);
            $table->boolean('footer_glass')->default(true);
            $table->json('footer_links')->nullable(); // [{label, url}]
            $table->json('footer_social_links')->nullable(); // [{platform, url}]
            $table->boolean('footer_show_copyright')->default(true);
            $table->boolean('footer_show_terms')->default(true);
            $table->boolean('footer_show_privacy')->default(true);
            $table->boolean('footer_show_docs')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'footer_sticky',
                'footer_glass',
                'footer_links',
                'footer_social_links',
                'footer_show_copyright',
                'footer_show_terms',
                'footer_show_privacy',
                'footer_show_docs',
            ]);
        });
    }
};
