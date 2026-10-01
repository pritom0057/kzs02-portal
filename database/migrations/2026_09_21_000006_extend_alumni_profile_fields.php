<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make roll_number nullable (raw SQL — no doctrine/dbal needed)
        DB::statement('ALTER TABLE alumni MODIFY roll_number VARCHAR(20) NULL');

        Schema::table('alumni', function (Blueprint $table) {
            $table->string('mobile')->nullable()->after('phone');
            $table->string('whatsapp')->nullable()->after('mobile');
            $table->string('father_name')->nullable()->after('whatsapp');
            $table->string('mother_name')->nullable()->after('father_name');
            $table->text('present_address')->nullable()->after('mother_name');
            $table->text('permanent_address')->nullable()->after('present_address');
            $table->string('school_shift')->nullable()->after('permanent_address'); // Morning / Day
            $table->string('section')->nullable()->after('school_shift');           // A / B
            $table->string('higher_education')->nullable()->after('section');
            $table->string('organization')->nullable()->after('higher_education');
            $table->string('designation')->nullable()->after('organization');
            $table->string('spouse_name')->nullable()->after('designation');
            $table->string('spouse_contact')->nullable()->after('spouse_name');
            $table->json('children')->nullable()->after('spouse_contact');
            $table->string('facebook_url')->nullable()->after('children');
            $table->string('linkedin_url')->nullable()->after('facebook_url');
            $table->boolean('show_in_directory')->default(true)->after('linkedin_url');
        });
    }

    public function down(): void
    {
        Schema::table('alumni', function (Blueprint $table) {
            $table->dropColumn([
                'mobile', 'whatsapp', 'father_name', 'mother_name',
                'present_address', 'permanent_address', 'school_shift', 'section',
                'higher_education', 'organization', 'designation',
                'spouse_name', 'spouse_contact', 'children',
                'facebook_url', 'linkedin_url', 'show_in_directory',
            ]);
        });
        DB::statement('ALTER TABLE alumni MODIFY roll_number VARCHAR(20) NOT NULL');
    }
};
