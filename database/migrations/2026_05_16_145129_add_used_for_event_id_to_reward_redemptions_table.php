<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsedForEventIdToRewardRedemptionsTable extends Migration
{
    public function up()
    {
        Schema::table('reward_redemptions', function (Blueprint $table) {
            $table->foreignId('used_for_event_id')->nullable()->after('reward_id')->constrained('events')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('reward_redemptions', function (Blueprint $table) {
            $table->dropForeign(['used_for_event_id']);
            $table->dropColumn('used_for_event_id');
        });
    }
}
