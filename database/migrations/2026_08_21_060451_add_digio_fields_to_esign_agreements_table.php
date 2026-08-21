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
            $table->string('digio_document_id')->nullable()->after('document_path');
            $table->text('esign_url')->nullable()->after('digio_document_id');
            $table->string('status')->default('pending')->after('esign_url');
            $table->boolean('is_signed')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('esign_agreements', function (Blueprint $table) {
            $table->dropColumn(['digio_document_id', 'esign_url', 'status', 'is_signed']);
        });
    }
};
