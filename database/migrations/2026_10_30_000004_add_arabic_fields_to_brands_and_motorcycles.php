<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The public site is bilingual — every display string gets an
        // Arabic counterpart, falling back to the base (EN) column.
        Schema::table('brands', function (Blueprint $table) {
            $table->string('name_ar', 80)->nullable()->after('name');
            $table->string('origin_ar', 80)->nullable()->after('origin');
            $table->string('tagline_ar', 140)->nullable()->after('tagline');
        });

        Schema::table('motorcycles', function (Blueprint $table) {
            $table->string('name_ar', 120)->nullable()->after('name');
            $table->string('tagline_ar', 160)->nullable()->after('tagline');
            $table->text('description_ar')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn(['name_ar', 'origin_ar', 'tagline_ar']);
        });
        Schema::table('motorcycles', function (Blueprint $table) {
            $table->dropColumn(['name_ar', 'tagline_ar', 'description_ar']);
        });
    }
};
