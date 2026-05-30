<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class EnsureMissingColumnsExist extends Migration
{
    public function up()
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'is_amil')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_amil')->default(false);
            });
        }

        if (Schema::hasTable('withdrawal_requests') && !Schema::hasColumn('withdrawal_requests', 'fund_purpose')) {
            Schema::table('withdrawal_requests', function (Blueprint $table) {
                $table->string('fund_purpose', 100)->nullable();
            });
            DB::statement("UPDATE withdrawal_requests SET fund_purpose = 'General Fund' WHERE fund_purpose IS NULL");
        }

        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->text('value')->nullable();
                $table->timestamps();
            });

            $adminCode = env('ADMIN_CODE');
            $treasurerCode = env('TREASURER_CODE');
            if ($adminCode || $treasurerCode) {
                DB::table('settings')->insert(array_filter([
                    $adminCode ? ['key' => 'admin_code', 'value' => $adminCode, 'created_at' => now(), 'updated_at' => now()] : null,
                    $treasurerCode ? ['key' => 'treasurer_code', 'value' => $treasurerCode, 'created_at' => now(), 'updated_at' => now()] : null,
                ]));
            }
        }
    }

    public function down()
    {
        //
    }
}
