<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVolunteerProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('volunteer_profiles', function (Blueprint $table) {
            $table->id();
            // One user has ONE profile
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->unique();
            // Storing skills as JSON: ["Cooking", "Cleaning"]
            $table->json('skills')->nullable();
            // Storing availability as JSON: {"weekend": true, "weekday": false}
            $table->json('availability')->nullable();
            $table->integer('experience_years')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
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
        Schema::dropIfExists('volunteer_profiles');
    }
}
