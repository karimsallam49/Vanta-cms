<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->string('scope', 20)->default('brand')->after('brand_id');
            $table->foreignId('motorcycle_id')->nullable()->after('scope')
                ->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('discount_percent')->nullable()->after('badge');
        });

        // Existing rows: NULL brand = group-wide, otherwise brand-scoped.
        DB::table('offers')->whereNull('brand_id')->update(['scope' => 'group']);
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('motorcycle_id');
            $table->dropColumn(['scope', 'discount_percent']);
        });
    }
};
