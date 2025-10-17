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
            $table->string('site_name')->nullable()->after('id');
            $table->string('tagline')->nullable()->after('site_name');
            $table->string('logo_path')->nullable()->after('tagline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_infos', function (Blueprint $table): void {
            $table->dropColumn(['site_name', 'tagline', 'logo_path']);
        });
    }
};
