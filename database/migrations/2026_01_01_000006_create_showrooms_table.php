<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('showrooms', function (Blueprint $table) {
            $table->id();
            $table->string('city');
            $table->string('country');
            $table->string('name');
            $table->string('address');
            $table->string('phone')->nullable();
            $table->string('hours')->nullable();
            $table->boolean('is_service_center')->default(false);
            $table->timestamps();

            $table->index(['country', 'city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('showrooms');
    }
};
