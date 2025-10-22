<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_infos', function (Blueprint $table): void {
            $table->string('hero_media_type')->nullable()->after('hero_secondary_cta_url');
            $table->string('hero_media_path')->nullable()->after('hero_media_type');
            $table->string('stat_one_icon')->nullable()->after('stat_one_label');
            $table->string('stat_two_icon')->nullable()->after('stat_two_label');
            $table->string('stat_three_icon')->nullable()->after('stat_three_label');
        });
    }

    public function down(): void
    {
        Schema::table('company_infos', function (Blueprint $table): void {
            $table->dropColumn([
                'hero_media_type',
                'hero_media_path',
                'stat_one_icon',
                'stat_two_icon',
                'stat_three_icon',
            ]);
        });
    }
};
