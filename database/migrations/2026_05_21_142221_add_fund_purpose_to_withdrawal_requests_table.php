<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddFundPurposeToWithdrawalRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('withdrawal_requests', 'fund_purpose')) {
                $table->string('fund_purpose', 100)->nullable()->after('type');
            }
        });

        DB::statement("UPDATE withdrawal_requests SET fund_purpose = 'General Fund' WHERE fund_purpose IS NULL");
    }

    public function down()
    {
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->dropColumn('fund_purpose');
        });
    }
}
