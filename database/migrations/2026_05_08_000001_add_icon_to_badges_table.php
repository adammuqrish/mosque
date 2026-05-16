<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIconToBadgesTable extends Migration
{
    public function up()
    {
        Schema::table('badges', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('icon_svg');
        });
    }

    public function down()
    {
        Schema::table('badges', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }
}
