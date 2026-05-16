<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttendanceFieldsToEventVolunteerTable extends Migration
{
    public function up()
    {
        Schema::table('event_volunteer', function (Blueprint $table) {
            // STEP 1: Add new attendance status field (replaces the simple 'status')
            // Options: confirmed, pending_review, completed, absent
            $table->enum('attendance_status', ['confirmed', 'pending_review', 'completed', 'absent'])
                  ->default('confirmed')
                  ->after('status');

            // STEP 2: Add reason field for absence (optional)
            $table->text('absence_reason')->nullable()->after('attendance_status');
        });
    }

    public function down()
    {
        Schema::table('event_volunteer', function (Blueprint $table) {
            $table->dropColumn(['attendance_status', 'absence_reason']);
        });
    }
}
