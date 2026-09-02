<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Promote the existing farm owners to administrators.
     */
    public function up(): void
    {
        DB::table('users')
            ->whereIn('id', [1, 2, 3])
            ->update([
                'is_admin' => true,
                'is_active' => true,
            ]);
    }

    /**
     * Revert the existing farm owners to non-admin users.
     */
    public function down(): void
    {
        DB::table('users')
            ->whereIn('id', [1, 2, 3])
            ->update([
                'is_admin' => false,
            ]);
    }
};
