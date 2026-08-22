<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kyc_verifications', function (Blueprint $table) {
            $table->boolean('declaration_accepted')->default(false)->after('status');
            $table->timestamp('declaration_accepted_at')->nullable()->after('declaration_accepted');
            $table->text('declaration_text')->nullable()->after('declaration_accepted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kyc_verifications', function (Blueprint $table) {
            $table->dropColumn(['declaration_accepted', 'declaration_accepted_at', 'declaration_text']);
        });
    }
};
