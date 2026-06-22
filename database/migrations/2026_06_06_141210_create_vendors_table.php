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
        Schema::table('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('contact_no', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('fax', 100)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('billing_address', 255)->nullable();
            $table->string('shipping_address', 255)->nullable();
            $table->foreignId('country_id')->comment('FK country_codes')->nullable()->constrained('country_codes');
            $table->tinyInteger('vendor_type')->nullable();
            $table->string('tin', 50)->nullable();
            $table->foreignId('created_by')->comment('FK user')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            //
        });
    }
};
