<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();  // Auto-incrementing primary key (bigint, unsigned)
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');  // Foreign key to the branches table
            $table->string('vendor_name', 255)->nullable();
            $table->string('ref_no', 255)->nullable();
            $table->tinyInteger('payment_method')->default(0)->comment('0 - Cash : 1 - Bank Transfer : 2 - Check : 3 - Gcash : 4 - Credit Card');
            $table->tinyInteger('tax_rate')->nullable();
            $table->decimal('sub_total', 12, 2)->nullable();
            $table->decimal('amount', 12, 2);
            $table->decimal('tax', 12, 2)->nullable();
            $table->text('description')->nullable();
            $table->date('expense_date');
            $table->string('slug', 100)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');  // Foreign key to users table
            $table->timestamps(0);  // Laravel will create `created_at` and `updated_at` timestamps

            // Add the primary key explicitly (optional)
            $table->primary('id');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
