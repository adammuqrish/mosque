<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdatePastEventsStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('events')
            ->where('event_date', '<', now())
            ->update(['status' => 'closed']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Optionally, set them back to 'open' if needed
        DB::table('events')
            ->where('event_date', '<', now())
            ->update(['status' => 'open']);
    }
}
