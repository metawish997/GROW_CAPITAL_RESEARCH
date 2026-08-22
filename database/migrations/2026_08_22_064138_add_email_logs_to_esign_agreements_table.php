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
        Schema::table('esign_agreements', function (Blueprint $table) {
            $table->json('email_logs')->nullable()->after('is_signed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('esign_agreements', function (Blueprint $table) {
            $table->dropColumn('email_logs');
        });
    }
};
