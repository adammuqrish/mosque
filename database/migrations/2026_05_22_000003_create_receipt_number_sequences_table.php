<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiptNumberSequencesTable extends Migration
{
    public function up()
    {
        Schema::create('receipt_number_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('prefix', 10);
            $table->char('year', 4);
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();
            $table->unique(['prefix', 'year']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('receipt_number_sequences');
    }
}
