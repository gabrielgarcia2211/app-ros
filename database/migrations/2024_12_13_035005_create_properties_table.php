<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('status');
            $table->unsignedBigInteger('owner_id');
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedInteger('property_type_id');
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('users');
            $table->foreign('tenant_id')->references('id')->on('users');
            $table->foreign('property_type_id')->references('id')->on('master_combos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
