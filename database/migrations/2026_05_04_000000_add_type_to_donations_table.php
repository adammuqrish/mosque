<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds Shariah-compliant donation classification fields:
     * - type: Categorizes donations as obligatory (Zakat), voluntary (Sadaqah/Infaq), or endowment (Waqf)
     * - asnaf_category: Tracks the 8 Zakat recipient categories for proper distribution compliance
     */
    public function up()
    {
        Schema::table('donations', function (Blueprint $table) {
            // Shariah-compliant type classification
            // 'obligatory' = Zakat, Zakat al-Fitr
            // 'voluntary' = Sadaqah, Sadaqah Jariyah, Infaq
            // 'endowment' = Waqf
            $table->enum('type', ['obligatory', 'voluntary', 'endowment'])->default('voluntary')->after('category');

            // 8 Asnaf categories for Zakat distribution tracking
            $table->string('asnaf_category')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn(['type', 'asnaf_category']);
        });
    }
};
