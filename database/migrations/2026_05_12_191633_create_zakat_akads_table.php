<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZakatAkadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zakat_akads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->constrained('donations')->cascadeOnDelete();
            $table->string('muzakki_name');
            $table->string('muzakki_ic')->nullable();
            $table->string('amil_name');
            $table->foreignId('amil_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('akad_date');
            $table->decimal('amount', 10, 2);
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('zakat_akads');
    }
}
