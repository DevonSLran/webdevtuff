<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // NOTE: This migration is a duplicate of 2026_04_21_045822_create_user_roles_table.php.
        // It is intentionally a no-op to avoid "table already exists" errors that
        // would otherwise stop subsequent migrations (e.g., orders) from running.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left blank.
    }
};
