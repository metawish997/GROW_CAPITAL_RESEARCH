<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('mobile');
            $table->string('address')->nullable()->after('phone');
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('pincode')->nullable()->after('state');
            $table->date('dob')->nullable()->after('pincode');
            $table->string('gender', 15)->nullable()->after('dob');
            $table->string('marital_status', 20)->nullable()->after('gender');
            $table->string('pan_card', 20)->nullable()->after('marital_status');
            $table->string('pan_card_name')->nullable()->after('pan_card');
            $table->string('father_name')->nullable()->after('pan_card_name');
        });
    }
 
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'address', 'city', 'state', 'pincode', 'dob',
                'gender', 'marital_status', 'pan_card', 'pan_card_name', 'father_name'
            ]);
        });
    }
};
