<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('motorcycles', function (Blueprint $table) {
            // Free-form spec table rows — [{"label":"ENGINE","value":"998 CC INLINE-4"}].
            // The numeric spec columns stay as the fallback when this is empty.
            $table->json('specs')->nullable()->after('top_speed_kmh');
            // Feature chips shown under the spec table on the public site.
            $table->json('features')->nullable()->after('colors');
        });
    }

    public function down(): void
    {
        Schema::table('motorcycles', function (Blueprint $table) {
            $table->dropColumn(['specs', 'features']);
        });
    }
};
