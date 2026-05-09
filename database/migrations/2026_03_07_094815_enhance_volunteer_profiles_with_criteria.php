<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnhanceVolunteerProfilesWithCriteria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('volunteer_profiles', function (Blueprint $table) {
            // JSON Fields (Banyak item)
            if (!Schema::hasColumn('volunteer_profiles', 'hobbies'))
                $table->json('hobbies')->nullable();
            if (!Schema::hasColumn('volunteer_profiles', 'interests'))
                $table->json('interests')->nullable();
            if (!Schema::hasColumn('volunteer_profiles', 'languages'))
                $table->json('languages')->nullable();

            // Text / Single Value Fields
            if (!Schema::hasColumn('volunteer_profiles', 'experience'))
                $table->text('experience')->nullable();
            if (!Schema::hasColumn('volunteer_profiles', 'location'))
                $table->string('location')->nullable();
            if (!Schema::hasColumn('volunteer_profiles', 'health_status'))
                $table->string('health_status')->nullable();
            if (!Schema::hasColumn('volunteer_profiles', 'long_term_availability'))
                $table->text('long_term_availability')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('volunteer_profiles', function (Blueprint $table) {
            //
        });
    }
}
