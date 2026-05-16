<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddFundPurposeToDonationsTable extends Migration
{
    public function up()
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->string('fund_purpose')->nullable()->after('category');
        });

        // Migrate existing category data to the new Shariah Type + Fund Purpose split
        DB::statement("
            UPDATE donations SET
                category = CASE
                    WHEN category IN ('zakat', 'zakat_fitr') THEN category
                    WHEN category = 'waqf' THEN category
                    ELSE 'sadaqah'
                END,
                fund_purpose = CASE
                    WHEN category = 'zakat' THEN 'General Fund'
                    WHEN category = 'zakat_fitr' THEN 'General Fund'
                    WHEN category = 'sadaqah' THEN 'Sadaqah (General)'
                    WHEN category = 'sadaqah_jariyah' THEN 'Sadaqah Jariyah'
                    WHEN category = 'infaq' THEN 'Infaq'
                    WHEN category = 'waqf' THEN 'General Fund'
                    WHEN category = 'operations' THEN 'Operations'
                    WHEN category = 'construction' THEN 'Construction'
                    WHEN category = 'education_community' THEN 'Education'
                    WHEN category = 'humanitarian' THEN 'Humanitarian'
                    WHEN category = 'other' THEN 'Other'
                    ELSE 'General Fund'
                END
        ");
    }

    public function down()
    {
        // Cannot reliably reverse the data migration, so just drop the column
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn('fund_purpose');
        });
    }
}
