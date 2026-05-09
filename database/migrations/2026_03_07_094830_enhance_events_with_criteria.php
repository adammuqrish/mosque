<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnhanceEventsWithCriteria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            // Criteria yang Event perlukan
            if (!Schema::hasColumn('events', 'required_hobbies'))
                $table->json('required_hobbies')->nullable();
            if (!Schema::hasColumn('events', 'required_languages'))
                $table->json('required_languages')->nullable();
            if (!Schema::hasColumn('events', 'event_location'))
                $table->string('event_location')->nullable(); // Lokasi event berlangsung
            if (!Schema::hasColumn('events', 'location_radius'))
                $table->string('location_radius')->nullable()->default('Any');
            if (!Schema::hasColumn('events', 'health_requirement'))
                $table->string('health_requirement')->nullable(); // Contoh: Fit, Bolehangkat barang
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            //
        });
    }
}
