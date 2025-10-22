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
        Schema::table('company_infos', function (Blueprint $table): void {
            $table->string('hero_headline')->nullable()->after('logo_path');
            $table->text('hero_subheadline')->nullable()->after('hero_headline');
            $table->string('hero_primary_cta_label')->nullable()->after('hero_subheadline');
            $table->string('hero_primary_cta_url')->nullable()->after('hero_primary_cta_label');
            $table->string('hero_secondary_cta_label')->nullable()->after('hero_primary_cta_url');
            $table->string('hero_secondary_cta_url')->nullable()->after('hero_secondary_cta_label');

            $table->string('stat_one_label')->nullable()->after('hero_secondary_cta_url');
            $table->string('stat_one_value')->nullable()->after('stat_one_label');
            $table->string('stat_two_label')->nullable()->after('stat_one_value');
            $table->string('stat_two_value')->nullable()->after('stat_two_label');
            $table->string('stat_three_label')->nullable()->after('stat_two_value');
            $table->string('stat_three_value')->nullable()->after('stat_three_label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_infos', function (Blueprint $table): void {
            $table->dropColumn([
                'hero_headline',
                'hero_subheadline',
                'hero_primary_cta_label',
                'hero_primary_cta_url',
                'hero_secondary_cta_label',
                'hero_secondary_cta_url',
                'stat_one_label',
                'stat_one_value',
                'stat_two_label',
                'stat_two_value',
                'stat_three_label',
                'stat_three_value',
            ]);
        });
    }
};

