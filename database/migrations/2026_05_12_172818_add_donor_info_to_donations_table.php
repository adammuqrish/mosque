<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDonorInfoToDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->string('donor_name')->nullable()->after('user_id');
            $table->string('donor_ic')->nullable()->after('donor_name');
            $table->string('donor_phone')->nullable()->after('donor_ic');
            $table->string('donor_email')->nullable()->after('donor_phone');
            $table->text('donor_address')->nullable()->after('donor_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn(['donor_name', 'donor_ic', 'donor_phone', 'donor_email', 'donor_address']);
        });
    }
}
