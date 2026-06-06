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
        Schema::table('client_group_tagging', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->comment('FK client')->nullable()->constrained('clients');
            $table->foreignId('client_group_type_id')->nullable()->constrained('client_group_types');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_group_tagging', function (Blueprint $table) {
            //
        });
    }
};
