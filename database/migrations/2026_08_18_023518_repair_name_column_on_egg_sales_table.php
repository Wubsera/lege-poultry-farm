<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the missing buyer name column.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('egg_sales', 'name')) {
            Schema::table('egg_sales', function (Blueprint $table) {
                $table->string('name')->nullable()->after('sale_date');
            });
        }
    }

    /**
     * Safely remove the buyer name column.
     */
    public function down(): void
    {
        if (Schema::hasColumn('egg_sales', 'name')) {
            Schema::table('egg_sales', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }
    }
};
