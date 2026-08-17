<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Egg Productions
        |--------------------------------------------------------------------------
        */

        Schema::table('egg_productions', function (Blueprint $table) {
            $table->foreignId('farm_id')
                ->nullable()
                ->after('id')
                ->index();
        });


        /*
        |--------------------------------------------------------------------------
        | Egg Sales
        |--------------------------------------------------------------------------
        */

        Schema::table('egg_sales', function (Blueprint $table) {
            $table->foreignId('farm_id')
                ->nullable()
                ->after('id')
                ->index();
        });


        /*
        |--------------------------------------------------------------------------
        | Expenses
        |--------------------------------------------------------------------------
        */

        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('farm_id')
                ->nullable()
                ->after('id')
                ->index();
        });


        /*
        |--------------------------------------------------------------------------
        | Flock Records
        |--------------------------------------------------------------------------
        */

        Schema::table('flock_records', function (Blueprint $table) {
            $table->foreignId('farm_id')
                ->nullable()
                ->after('id')
                ->index();
        });


        /*
        |--------------------------------------------------------------------------
        | Assign Existing Records to Lege Poultry Farm
        |--------------------------------------------------------------------------
        |
        | Farm #1 is the existing Lege Poultry Farm.
        |
        */

        DB::table('egg_productions')
            ->whereNull('farm_id')
            ->update([
                'farm_id' => 1,
            ]);

        DB::table('egg_sales')
            ->whereNull('farm_id')
            ->update([
                'farm_id' => 1,
            ]);

        DB::table('expenses')
            ->whereNull('farm_id')
            ->update([
                'farm_id' => 1,
            ]);

        DB::table('flock_records')
            ->whereNull('farm_id')
            ->update([
                'farm_id' => 1,
            ]);


        /*
        |--------------------------------------------------------------------------
        | Add Foreign Keys
        |--------------------------------------------------------------------------
        */

        Schema::table('egg_productions', function (Blueprint $table) {
            $table->foreign('farm_id')
                ->references('id')
                ->on('farms')
                ->cascadeOnDelete();
        });

        Schema::table('egg_sales', function (Blueprint $table) {
            $table->foreign('farm_id')
                ->references('id')
                ->on('farms')
                ->cascadeOnDelete();
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->foreign('farm_id')
                ->references('id')
                ->on('farms')
                ->cascadeOnDelete();
        });

        Schema::table('flock_records', function (Blueprint $table) {
            $table->foreign('farm_id')
                ->references('id')
                ->on('farms')
                ->cascadeOnDelete();
        });
    }


    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Remove Foreign Keys
        |--------------------------------------------------------------------------
        */

        Schema::table('egg_productions', function (Blueprint $table) {
            $table->dropForeign(['farm_id']);
            $table->dropColumn('farm_id');
        });

        Schema::table('egg_sales', function (Blueprint $table) {
            $table->dropForeign(['farm_id']);
            $table->dropColumn('farm_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['farm_id']);
            $table->dropColumn('farm_id');
        });

        Schema::table('flock_records', function (Blueprint $table) {
            $table->dropForeign(['farm_id']);
            $table->dropColumn('farm_id');
        });
    }
};
