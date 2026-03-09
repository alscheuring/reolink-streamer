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
        Schema::table('cameras', function (Blueprint $table) {
            $table->boolean('has_ptz')->default(false);
            $table->string('ptz_username')->nullable();
            $table->string('ptz_password')->nullable();
            $table->string('ptz_api_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cameras', function (Blueprint $table) {
            $table->dropColumn(['has_ptz', 'ptz_username', 'ptz_password', 'ptz_api_url']);
        });
    }
};
