<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageFieldsToRewardsTable extends Migration
{
    public function up()
    {
        Schema::table('rewards', function (Blueprint $table) {
            $table->string('image', 500)->nullable()->after('category');
            $table->string('image_svg', 1000)->nullable()->after('image');
            $table->string('description_my', 1000)->nullable()->after('description');
            $table->integer('stock_quantity')->nullable()->unsigned()->after('points_cost');
        });
    }

    public function down()
    {
        Schema::table('rewards', function (Blueprint $table) {
            $table->dropColumn(['image', 'image_svg', 'description_my', 'stock_quantity']);
        });
    }
}