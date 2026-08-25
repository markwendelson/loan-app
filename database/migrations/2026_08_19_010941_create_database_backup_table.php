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
        Schema::create('database_backup', function (Blueprint $table) {
            $table->id();
            $table->string('file_name', 200)->nullable();
            $table->string('file_path', 200)->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->comment('FK user');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('database_backup');
    }
};
