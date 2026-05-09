<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('donations', function (Blueprint $table) {
            // Kita check dulu supaya tak error kalau column dah wujud
            if (!Schema::hasColumn('donations', 'user_id')) {
                // Buat column user_id dan setkan sebagai Foreign Key
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('donations', function (Blueprint $table) {
            // Cara nak drop column kalau perlu rollback nanti
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
}
