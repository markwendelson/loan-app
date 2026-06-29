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
        Schema::create('loans', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('branch_id')->nullable();
            $table->date('loan_date')->nullable();

            $table->string('loan_no', 191)->nullable();
            $table->string('reference_no', 100)->nullable();

            $table->integer('loan_product_id')->nullable();
            $table->integer('borrower_id')->nullable();

            $table->string('description', 191)->nullable();

            $table->date('release_date')->nullable();
            $table->date('maturity_date')->nullable();

            $table->tinyInteger('status')->default(0);

            $table->date('interest_start_date')->nullable();
            $table->date('first_payment_date')->nullable();

            $table->decimal('principal', 12, 2)->nullable();

            $table->enum('interest_method', [
                'flat_rate',
                'declining_balance_equal_installments',
                'declining_balance_equal_principal',
                'interest_only',
                'compound_interest'
            ])->nullable();

            $table->decimal('interest_rate', 12, 2)->nullable();

            $table->enum('interest_period', [
                'day', 'week', 'month', 'year'
            ])->nullable();

            $table->tinyInteger('override_interest')->nullable();
            $table->decimal('override_interest_amount', 12, 2)->nullable();

            $table->decimal('loan_duration', 11, 2)->nullable();
            $table->decimal('monthly_amortization', 12, 2)->nullable();

            $table->enum('repayment_cycle', [
                'day', 'week', 'month', 'year'
            ])->nullable();

            $table->longText('repayment_order')->nullable();

            $table->dateTime('approved_date')->nullable();
            $table->dateTime('disbursed_date')->nullable();
            $table->dateTime('closed_date')->nullable();
            $table->dateTime('declined_date')->nullable();

            $table->integer('approved_by')->nullable();
            $table->integer('disbursed_by')->nullable();
            $table->integer('declined_by')->nullable();

            $table->decimal('applied_amount', 12, 2)->nullable();

            $table->string('mode_of_payment', 191)->nullable();

            $table->string('approved_notes', 191)->nullable();
            $table->string('disbursed_notes', 191)->nullable();

            $table->boolean('is_special')->default(false);
            $table->boolean('is_reloan')->nullable();
            $table->boolean('is_additional_loan')->default(false);

            $table->string('reference_loan_no', 191)->nullable();
            $table->string('disbursement_reference', 191)->nullable();

            $table->integer('created_by')->nullable();

            $table->dateTime('created_at')->nullable();
            $table->softDeletes(); // deleted_at

            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->index('loan_product_id');
            $table->index('borrower_id');
        });

        Schema::create('loan_withdraw_requests', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('loan_id')->unique();
            $table->enum('status', ['completed', 'pending', 'cancelled'])->nullable();

            $table->integer('requested_by');
            $table->integer('updated_by')->nullable();

            $table->dateTime('created_at')->nullable();

            $table->timestamp('updated_at')
                ->nullable()
                ->useCurrent()
                ->useCurrentOnUpdate();
            $table->index('requested_by');
            $table->index('updated_by');
        });

        Schema::create('loan_types', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name', 100)->unique();
            $table->text('description');

            // 1 = active, 2 = inactive
            $table->tinyInteger('status')->default(1);

            $table->integer('interest_rate')->nullable();

            $table->dateTime('created_at')->nullable();

            $table->softDeletes(); // deleted_at

            $table->timestamp('updated_at')
                ->nullable()
                ->useCurrent()
                ->useCurrentOnUpdate();
        });

        Schema::create('loan_type_deductions', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('loan_type_id')->nullable()->comment('FK loan_type');
            $table->integer('loan_deduction_type_id')->nullable()->comment('FK loan_deduction_type');

            $table->float('percentage')->nullable();

            // 1 = capital, 2 = principal
            $table->tinyInteger('deduct_at')->default(0)
                ->comment('1 - capital : 2 - principal');

            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });

        Schema::create('loan_type', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name', 100)->nullable();
            $table->text('description')->nullable();

            $table->float('interest_rate', 7, 2)->nullable();

            // 1 = yes, 2 = no
            $table->tinyInteger('has_maturity')->nullable()
                ->comment('1 - yes : 2 - no');

            $table->tinyInteger('has_additional')->nullable()
                ->comment('1 - yes : 2 - no');

            // 1 = active, 2 = inactive
            $table->tinyInteger('status')->default(2)
                ->comment('1 - active : 2 - inactive');

            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });

        Schema::create('loan_term', function (Blueprint $table) {
            $table->increments('id');

            $table->smallInteger('value')->nullable();
            $table->string('name', 50)->nullable();

            // 1 = active, 2 = inactive
            $table->tinyInteger('status')->nullable()
                ->comment('1 - active : 2 - inactive');

            $table->dateTime('created_at')->nullable();

            $table->softDeletes(); // deleted_at

            $table->timestamp('updated_at')
                ->nullable()
                ->useCurrent()
                ->useCurrentOnUpdate();
        });

        Schema::create('loan_products', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('loan_type_id')->nullable();

            $table->string('name', 100)->nullable();
            $table->text('description')->nullable();

            // 1 = active, 2 = inactive
            $table->tinyInteger('status')->default(2);

            // JSON field (repayment order)
            $table->longText('repayment_order')->nullable();

            $table->dateTime('created_at')->nullable();

            $table->softDeletes(); // deleted_at

            $table->timestamp('updated_at')
                ->nullable()
                ->useCurrent()
                ->useCurrentOnUpdate();
        });

        Schema::create('loan_product_prerequisites', function (Blueprint $table) {

            $table->unsignedInteger('loan_product_id');
            $table->unsignedInteger('loan_product_id_required');

            // Optional but highly recommended for performance
            $table->index('loan_product_id');
            $table->index('loan_product_id_required');

            // Prevent duplicates
            $table->primary([
                'loan_product_id',
                'loan_product_id_required'
            ]);
        });

        Schema::create('loan_fees', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->unsignedBigInteger('loan_id')->nullable();

            $table->decimal('insurance_monthly_fee', 12, 2)->nullable();
            $table->decimal('insurance_annual_fee', 12, 2)->nullable();

            $table->decimal('monthly_amortization', 12, 2)->nullable();
        });

        Schema::create('loan_disbursement_details', function (Blueprint $table) {

            $table->increments('id');

            $table->integer('loan_id')->nullable()->comment('FK loan');
            $table->integer('processing_branch')->nullable();

            $table->decimal('amount', 10, 2)->nullable();

            $table->integer('disbursed_by')->nullable();

            $table->date('disburse_date')->nullable();

            $table->string('notes', 255)->nullable();

            // timestamps
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->nullable();
        });

        Schema::create('loan_deduction_types', function (Blueprint $table) {

            $table->increments('id'); // safer than column name "int"

            $table->string('name', 50)->nullable();

            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });

        Schema::create('loan_charges', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('loan_id')->nullable();

            $table->decimal('advance_interest', 12, 2)->nullable();
            $table->decimal('service_fee', 12, 2)->nullable();
            $table->decimal('insurance_fee', 12, 2)->nullable();
            $table->decimal('notary_fee', 12, 2)->nullable();
            $table->decimal('reloan_deduction', 12, 2)->nullable();

            $table->decimal('special_loan_balance', 12, 2)->nullable();

            $table->decimal('membership_fee', 12, 2)->nullable();
            $table->decimal('membership_fee_paid', 12, 2)->nullable();

            $table->decimal('principal_advance', 12, 2)->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
        Schema::dropIfExists('loan_withdraw_requests');
        Schema::dropIfExists('loan_types');
        Schema::dropIfExists('loan_type_deductions');
        Schema::dropIfExists('loan_type');
        Schema::dropIfExists('loan_term');
        Schema::dropIfExists('loan_products');
        Schema::dropIfExists('loan_product_prerequisites');
        Schema::dropIfExists('loan_fees');
        Schema::dropIfExists('loan_disbursement_details');
        Schema::dropIfExists('loan_deduction_types');
        Schema::dropIfExists('loan_charges');
        

    }
};
