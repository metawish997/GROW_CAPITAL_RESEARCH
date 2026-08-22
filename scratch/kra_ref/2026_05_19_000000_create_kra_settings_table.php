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
        Schema::create('kra_settings', function (Blueprint $table) {
            $table->id();
            
            // NDML SOAP Credentials
            $table->string('ndml_user_id')->nullable();
            $table->string('ndml_password')->nullable();
            $table->string('ndml_bp_id')->nullable();
            $table->string('ndml_passkey')->nullable();
            $table->string('ndml_encryption_key')->nullable();
            $table->boolean('ndml_uat_mode')->default(true);
            
            // SFTP Credentials
            $table->string('sftp_host')->nullable();
            $table->integer('sftp_port')->default(22);
            $table->string('sftp_username')->nullable();
            $table->string('sftp_password')->nullable();
            
            // Automation & General settings
            $table->boolean('auto_upload_on_approval')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kra_settings');
    }
};
