<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create on local database connection
        Schema::connection('mysql_local')->create('write_queue', function (Blueprint $table) {
            $table->id();
            $table->string('method'); // POST, PUT, PATCH, DELETE
            $table->text('url');
            $table->string('route')->nullable();
            $table->longText('payload'); // JSON encoded request data
            $table->foreignId('user_id')->nullable();
            $table->string('status')->default('pending'); // pending, completed, failed
            $table->integer('attempts')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql_local')->dropIfExists('write_queue');
    }
};
