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
        Schema::table('kra_settings', function (Blueprint $table) {
            $table->dropColumn([
                'ndml_user_id',
                'ndml_password',
                'ndml_bp_id',
                'ndml_passkey',
                'ndml_encryption_key',
                'ndml_uat_mode'
            ]);

            $table->string('kfin_user_id')->nullable();
            $table->string('kfin_password')->nullable();
            $table->string('kfin_pos_code')->nullable();
            $table->boolean('kfin_uat_mode')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kra_settings', function (Blueprint $table) {
            $table->string('ndml_user_id')->nullable();
            $table->string('ndml_password')->nullable();
            $table->string('ndml_bp_id')->nullable();
            $table->string('ndml_passkey')->nullable();
            $table->string('ndml_encryption_key')->nullable();
            $table->boolean('ndml_uat_mode')->default(true);

            $table->dropColumn([
                'kfin_user_id',
                'kfin_password',
                'kfin_pos_code',
                'kfin_uat_mode'
            ]);
        });
    }
};
