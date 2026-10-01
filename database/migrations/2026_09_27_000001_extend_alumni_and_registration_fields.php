<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumni', function (Blueprint $table) {
            $table->string('name_bn')->nullable()->after('name');
            $table->string('school_class')->nullable()->after('section');
            $table->string('emergency_contact')->nullable()->after('phone');
            $table->string('family_photo_url')->nullable()->after('photo_url');
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            $table->string('spouse_name_bn')->nullable()->after('spouse_name');
            $table->json('children_details')->nullable()->after('children_count');
            $table->unsignedTinyInteger('guests_count')->default(0)->after('children_count');
            $table->json('guests_details')->nullable()->after('guests_count');
            $table->boolean('driver_included')->default(false)->after('guests_details');
        });
    }

    public function down(): void
    {
        Schema::table('alumni', function (Blueprint $table) {
            $table->dropColumn(['name_bn', 'school_class', 'emergency_contact', 'family_photo_url']);
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropColumn(['spouse_name_bn', 'children_details', 'guests_count', 'guests_details', 'driver_included']);
        });
    }
};
