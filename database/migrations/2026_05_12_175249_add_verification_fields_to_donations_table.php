<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVerificationFieldsToDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->enum('status', ['pending', 'confirmed', 'disputed'])->default('pending')->after('source');
            $table->string('reference')->nullable()->after('status');
            $table->timestamp('verified_at')->nullable()->after('reference');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete()->after('verified_at');
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
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['status', 'reference', 'verified_at', 'verified_by']);
        });
    }
}
