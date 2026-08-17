<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'farm_id')) {

            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('farm_id')
                    ->nullable()
                    ->after('id')
                    ->index();
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Add Foreign Key
        |--------------------------------------------------------------------------
        */

        $foreignKeyExists = collect(
            Schema::getForeignKeys('users')
        )->contains(function ($foreignKey) {
            return in_array('farm_id', $foreignKey['columns']);
        });

        if (! $foreignKeyExists) {

            Schema::table('users', function (Blueprint $table) {
                $table->foreign('farm_id')
                    ->references('id')
                    ->on('farms')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'farm_id')) {

            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['farm_id']);
                $table->dropColumn('farm_id');
            });
        }
    }
};
