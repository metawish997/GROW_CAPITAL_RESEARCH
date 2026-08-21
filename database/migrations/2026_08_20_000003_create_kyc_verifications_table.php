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
        Schema::create('kyc_verifications', function (Blueprint $table) {
            $table->id();

            // Link to users table
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->string('digio_document_id')->unique();

            $table->string('customer_name');
            $table->string('customer_mobile');
            $table->string('customer_email')->nullable();

            $table->string('reference_id')->unique();
            $table->string('transaction_id')->nullable();

            // initiated | pending | approval_pending | approved | rejected | failed | expired
            $table->string('status')->default('pending');

            $table->json('kyc_details')->nullable();
            $table->json('aadhaar_details')->nullable();

            $table->string('callback_status')->nullable();
            $table->text('callback_message')->nullable();

            $table->timestamp('kyc_completed_at')->nullable();
            $table->timestamp('kyc_expires_at')->nullable();

            $table->json('raw_response')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['customer_mobile', 'status']);
            $table->index(['reference_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kyc_verifications');
    }
};
