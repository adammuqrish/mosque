<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddZakatFitrToWithdrawalType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE withdrawal_requests MODIFY COLUMN type ENUM('zakat','zakat_fitr','sadaqah','waqf') NOT NULL DEFAULT 'sadaqah'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE withdrawal_requests MODIFY COLUMN type ENUM('zakat','sadaqah','waqf') NOT NULL DEFAULT 'sadaqah'");
    }
}
