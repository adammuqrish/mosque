<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2); // 10 digits total, 2 decimal places
            $table->string('category')->default('General'); // Zakat, Sedekah, etc.
            $table->enum('source', ['cash', 'online'])->default('cash');
            $table->text('description')->nullable();
            $table->date('donation_date'); // When was the money given?
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donations');
    }
}
