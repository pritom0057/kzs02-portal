<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->boolean('bring_spouse')->default(false)->after('guest_count');
            $table->string('spouse_name')->nullable()->after('bring_spouse');
            $table->unsignedTinyInteger('children_count')->default(0)->after('spouse_name');
            $table->enum('meal_preference', [
                'no_preference', 'vegetarian', 'non_vegetarian', 'vegan'
            ])->default('no_preference')->after('children_count');
            $table->text('special_requirements')->nullable()->after('meal_preference');
            $table->decimal('total_amount', 10, 2)->default(0)->after('special_requirements');
            $table->enum('payment_method', ['bkash', 'nagad', 'bank_transfer', 'sslcommerz'])
                ->nullable()->after('total_amount');
            $table->string('payment_reference')->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropColumn([
                'bring_spouse', 'spouse_name', 'children_count',
                'meal_preference', 'special_requirements', 'total_amount',
                'payment_method', 'payment_reference',
            ]);
        });
    }
};
