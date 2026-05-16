<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundPurposesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fund_purposes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('fund_purposes')->insert([
            ['name' => 'General Fund', 'sort_order' => 1],
            ['name' => 'Kipas Gergasi', 'sort_order' => 2],
            ['name' => 'Aircond', 'sort_order' => 3],
            ['name' => 'Karpet Baru', 'sort_order' => 4],
            ['name' => 'Construction', 'sort_order' => 5],
            ['name' => 'Operations', 'sort_order' => 6],
            ['name' => 'Education', 'sort_order' => 7],
            ['name' => 'Humanitarian', 'sort_order' => 8],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('fund_purposes');
    }
}
