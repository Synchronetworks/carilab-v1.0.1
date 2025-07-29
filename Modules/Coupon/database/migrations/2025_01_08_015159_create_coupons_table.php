<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            

            $table->id();
            $table->unsignedBigInteger('vendor_id')->nullable(); // Optional vendor ID
            $table->unsignedBigInteger('lab_id')->nullable();
            $table->string('coupon_code', 50)->unique(); // Coupon code
            $table->enum('discount_type', ['percentage', 'fixed']); // Discount type
            $table->decimal('discount_value', 8, 2); // Discount value
            $table->json('applicability')->nullable(); // Applicability (JSON for multi-select options)
            $table->date('start_at'); // Start date
            $table->date('end_at'); // End date
            $table->unsignedInteger('total_usage_limit'); // Total usage count
            $table->unsignedInteger('per_customer_usage_limit'); // Per-customer usage count
            $table->boolean('status')->default(1); // Active status


            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // Pivot table for coupon to test mapping
        Schema::create('coupon_test_mapping', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coupon_id');
            $table->unsignedBigInteger('test_id');
            $table->timestamps();
        });

        // Pivot table for coupon to package mapping
        Schema::create('coupon_package_mapping', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coupon_id');
            $table->unsignedBigInteger('package_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupon_test_mapping');
        Schema::dropIfExists('coupon_package_mapping');
        Schema::dropIfExists('coupons');
    }
};
