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
        Schema::create('reference_no_counter', function (Blueprint $table) {

            $table->id();

            $table->string('model', 50);
            $table->unsignedBigInteger('branch_id');

            $table->bigInteger('current_number');
            $table->string('prefix', 50)->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')
                ->useCurrent()
                ->useCurrentOnUpdate();

            // ✅ Important indexes (production-ready)
            $table->unique(['model', 'branch_id']);

            $table->index('model');
            $table->index('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reference_no_counter');
    }
};
