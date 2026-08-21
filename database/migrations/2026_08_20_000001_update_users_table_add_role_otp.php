<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('email'); // 'user' or 'admin'
            $table->string('otp', 6)->nullable()->after('role');
            $table->timestamp('otp_expires_at')->nullable()->after('otp');
            $table->string('mobile', 15)->nullable()->after('name');
            $table->string('password')->nullable()->change(); // Users login via OTP, no password needed
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'otp', 'otp_expires_at', 'mobile']);
            $table->string('password')->nullable(false)->change();
        });
    }
};
