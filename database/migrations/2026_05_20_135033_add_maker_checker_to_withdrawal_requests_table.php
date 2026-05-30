<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddMakerCheckerToWithdrawalRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->foreignId('maker_checked_by')->nullable()->constrained('users')->after('approved_by');
            $table->timestamp('maker_checked_at')->nullable()->after('maker_checked_by');
        });

        DB::statement("ALTER TABLE withdrawal_requests MODIFY COLUMN status ENUM('pending', 'maker_checked', 'approved', 'rejected') DEFAULT 'pending'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE withdrawal_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");

        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->dropForeign(['maker_checked_by']);
            $table->dropColumn(['maker_checked_by', 'maker_checked_at']);
        });
    }
}
