<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRejectionReasonToWithdrawalRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('withdrawal_requests', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('status');
            }
        });
    }

    public function down()
    {
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });
    }
}