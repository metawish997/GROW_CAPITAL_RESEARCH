<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Contact info
            $table->string('phone')->unique()->after('password');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();
            $table->string('country')->nullable();

            // Role / permission reference
            $table->unsignedBigInteger('role_id')->nullable();

            // Profile
            $table->string('image')->nullable();
            $table->text('bio')->nullable();
            $table->string('language_preference')->nullable();

            // Personal details
            $table->date('dob')->nullable();
            $table->enum('gender', ['male','female','other'])->nullable();
            $table->enum('marital_status', ['single','married','divorced','widowed','other'])->nullable();
            $table->string('blood_group')->nullable();

            // KYC Documents
            $table->string('adhar_card')->nullable();
            $table->string('adhar_card_name')->nullable(); 
            $table->string('pan_card')->nullable(); 
            $table->string('pan_card_name')->nullable();

            // Business details
            $table->string('business_name')->nullable();
            $table->string('business_type')->nullable();
            $table->string('business_document')->nullable();

            // Education details
            $table->string('education_institute')->nullable();
            $table->string('education_degree')->nullable();
            $table->string('education_document')->nullable();

            // Social / web links
            $table->string('website')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('twitter')->nullable();
            $table->string('facebook')->nullable();
            $table->json('social_links')->nullable(); 

            // Extra optional fields
            $table->text('hobbies')->nullable();
            $table->text('skills')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone','address','city','state','pincode','country','role_id',
                'image','bio','language_preference','dob','gender','marital_status','blood_group',
                'adhar_card','adhar_card_name','pan_card','pan_card_name',
                'business_name','business_type','business_document',
                'education_institute','education_degree','education_document',
                'website','linkedin','twitter','facebook','social_links',
                'hobbies','skills','emergency_contact_name','emergency_contact_phone'
            ]);
        });
    }
};
