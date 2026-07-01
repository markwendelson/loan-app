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
        if (!Schema::hasTable('walkins')) {
            Schema::create('walkins', function (Blueprint $table) {
                $table->id();
                $table->integer('branch_id')->nullable();
                $table->date('date');
                $table->string('reference_no', 100)->nullable();
                $table->string('last_name');
                $table->string('first_name');
                $table->string('middle_initial')->nullable();
                $table->decimal('amount_withdrawn', 10, 2)->nullable();
                $table->decimal('charge', 10, 2)->nullable();
                $table->decimal('net_proceeds', 10, 2);
                $table->boolean('no_service_charge')
                    ->default(false)
                    ->comment('1 - has service charge : 0 - no service charge');
                $table->softDeletes(); // handles deleted_at (datetime)
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('walkins');
    }
};
