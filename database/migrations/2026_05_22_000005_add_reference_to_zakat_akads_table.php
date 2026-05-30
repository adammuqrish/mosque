<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferenceToZakatAkadsTable extends Migration
{
    public function up()
    {
        Schema::table('zakat_akads', function (Blueprint $table) {
            $table->string('reference', 30)->nullable()->unique()->after('donation_id');
        });
    }

    public function down()
    {
        Schema::table('zakat_akads', function (Blueprint $table) {
            $table->dropColumn('reference');
        });
    }
}
