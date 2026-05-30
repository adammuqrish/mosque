<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawalDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('withdrawal_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('withdrawal_request_id')->constrained('withdrawal_requests')->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type', 50);
            $table->integer('file_size');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('withdrawal_documents');
    }
}
