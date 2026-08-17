<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flock_records', function (Blueprint $table) {
            $table->id();

            $table->date('record_date');

            $table->integer('total_birds')->default(1000);

            $table->integer('sick')->default(0);

            $table->integer('recovered')->default(0);

            $table->integer('dead')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flock_records');
    }
};
