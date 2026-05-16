<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGamificationFieldsToExistingTables extends Migration
{
    public function up()
    {
        // Users: Referral tracking & leaderboard privacy
        Schema::table('users', function (Blueprint $table) {
            $table->string('referred_code', 20)->nullable()->unique()->after('phone');
            $table->foreignId('referred_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('hide_from_leaderboard')->default(false)->after('referred_by');
        });

        // event_volunteer: Gamification tracking
        Schema::table('event_volunteer', function (Blueprint $table) {
            $table->boolean('points_awarded')->default(false)->after('absence_reason');
            $table->integer('points_earned')->default(0)->after('points_awarded');
        });

        // events: Gamification category
        Schema::table('events', function (Blueprint $table) {
            $table->string('gamification_category')->default('general')->after('status');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropColumn(['referred_code', 'referred_by', 'hide_from_leaderboard']);
        });
        
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('gamification_category');
        });
        
        Schema::table('event_volunteer', function (Blueprint $table) {
            $table->dropColumn(['points_awarded', 'points_earned']);
        });
    }
}
