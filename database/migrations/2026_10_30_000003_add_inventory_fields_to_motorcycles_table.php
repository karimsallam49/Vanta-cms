<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('motorcycles', function (Blueprint $table) {
            $table->foreignId('currency_id')->nullable()->after('monthly_from')
                ->constrained()->nullOnDelete();
            $table->text('description')->nullable()->after('tagline');
            $table->boolean('is_active')->default(true)->after('colors');
            $table->unsignedInteger('quantity')->default(0)->after('is_active');
        });

        // A model is stocked in many showrooms; a showroom carries many models.
        Schema::create('motorcycle_showroom', function (Blueprint $table) {
            $table->foreignId('motorcycle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('showroom_id')->constrained()->cascadeOnDelete();
            $table->primary(['motorcycle_id', 'showroom_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motorcycle_showroom');

        Schema::table('motorcycles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('currency_id');
            $table->dropColumn(['description', 'is_active', 'quantity']);
        });
    }
};
