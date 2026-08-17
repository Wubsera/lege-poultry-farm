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
        | Create Farms Table
        |--------------------------------------------------------------------------
        */

        Schema::create('farms', function (Blueprint $table) {
            $table->id();
            $table->string('farm_name');
            $table->date('registration_date')->nullable();
            $table->timestamps();
        });


        /*
        |--------------------------------------------------------------------------
        | Create Farm #1 From Existing Farm Settings
        |--------------------------------------------------------------------------
        */

        $farmSetting = DB::table('farm_settings')->first();

        if ($farmSetting) {

            $farmId = DB::table('farms')->insertGetId([
                'farm_name' => $farmSetting->farm_name,
                'registration_date' => $farmSetting->registration_date,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        } else {

            $farmId = DB::table('farms')->insertGetId([
                'farm_name' => 'Lege Poultry Farm',
                'registration_date' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Add farm_id To Farm Settings
        |--------------------------------------------------------------------------
        */

        Schema::table('farm_settings', function (Blueprint $table) {
            $table->foreignId('farm_id')
                ->nullable()
                ->after('id');
        });


        /*
        |--------------------------------------------------------------------------
        | Link Existing Farm Settings To Farm #1
        |--------------------------------------------------------------------------
        */

        DB::table('farm_settings')
            ->where('id', $farmSetting?->id ?? 0)
            ->update([
                'farm_id' => $farmId,
            ]);


        /*
        |--------------------------------------------------------------------------
        | Add Foreign Key
        |--------------------------------------------------------------------------
        */

        Schema::table('farm_settings', function (Blueprint $table) {
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
        | Remove Foreign Key And farm_id
        |--------------------------------------------------------------------------
        */

        Schema::table('farm_settings', function (Blueprint $table) {
            $table->dropForeign(['farm_id']);
            $table->dropColumn('farm_id');
        });


        /*
        |--------------------------------------------------------------------------
        | Remove Farms Table
        |--------------------------------------------------------------------------
        */

        Schema::dropIfExists('farms');
    }
};
