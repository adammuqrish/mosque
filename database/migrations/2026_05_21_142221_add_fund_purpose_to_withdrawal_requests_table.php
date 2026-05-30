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
            $table->string('fund_purpose', 100)->nullable()->after('type');
        });

        DB::statement("UPDATE withdrawal_requests SET fund_purpose = 'General Fund' WHERE fund_purpose IS NULL");

        DB::statement("ALTER TABLE withdrawal_requests MODIFY fund_purpose VARCHAR(100) NOT NULL");
    }

    public function down()
    {
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->dropColumn('fund_purpose');
        });
    }
}
