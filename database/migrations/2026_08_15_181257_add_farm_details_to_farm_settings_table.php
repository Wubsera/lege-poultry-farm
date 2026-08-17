<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farm_settings', function (Blueprint $table) {
            $table->string('farm_name')->default('Lege Poultry Farm');
            $table->date('registration_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('farm_settings', function (Blueprint $table) {
            $table->dropColumn([
                'farm_name',
                'registration_date',
            ]);
        });
    }
};
