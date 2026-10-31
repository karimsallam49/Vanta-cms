<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('showrooms', function (Blueprint $table) {
            // Weekday numbers the showroom takes test rides (0 = Sunday … 6 = Saturday).
            // Null means "open every day" — keeps old rows valid.
            $table->json('open_days')->nullable()->after('hours');
        });
    }

    public function down(): void
    {
        Schema::table('showrooms', function (Blueprint $table) {
            $table->dropColumn('open_days');
        });
    }
};
