<?php

use App\Support\Permissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            // Full-access flag — skips every permission check, like the old
            // 'admin' enum value. Everything else is whitelisted per section.
            $table->boolean('is_admin')->default(false);
            $table->json('permissions')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('remember_token')
                ->constrained('roles')->nullOnDelete();
        });

        // Backfill: map the old enum column onto seeded roles.
        $adminId = DB::table('roles')->insertGetId([
            'name' => 'Administrator', 'slug' => 'administrator',
            'description' => 'Full access to every section — bypasses all permission checks.',
            'is_admin' => true, 'permissions' => json_encode([]),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $entryId = DB::table('roles')->insertGetId([
            'name' => 'Data Entry', 'slug' => 'data-entry',
            'description' => 'Manages assigned brands: motorcycles, offers and test-ride requests.',
            'is_admin' => false, 'permissions' => json_encode(Permissions::dataEntryDefaults()),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        DB::table('users')->where('role', 'admin')->update(['role_id' => $adminId]);
        DB::table('users')->where('role', 'data_entry')->update(['role_id' => $entryId]);
        // Any unexpected value lands on the least-privileged role.
        DB::table('users')->whereNull('role_id')->update(['role_id' => $entryId]);

        Schema::table('users', function (Blueprint $table) {
            // The filter index references the column — drop it first.
            $table->dropIndex(['role']);
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('data_entry')->after('remember_token');
        });
        DB::table('users')->update(['role' => 'data_entry']);
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
        });
        Schema::dropIfExists('roles');
    }
};
