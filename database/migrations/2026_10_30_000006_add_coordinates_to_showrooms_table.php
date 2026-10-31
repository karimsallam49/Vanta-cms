<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('showrooms', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('hours');
            $table->decimal('longitude', 11, 7)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('showrooms', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
