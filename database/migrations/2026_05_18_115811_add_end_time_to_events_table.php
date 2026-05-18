<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddEndTimeToEventsTable extends Migration
{
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dateTime('end_time')->nullable()->after('event_date');
        });

        DB::table('events')
            ->whereNull('end_time')
            ->update([
                'end_time' => DB::raw('DATE_ADD(event_date, INTERVAL 2 HOUR)')
            ]);
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('end_time');
        });
    }
}
