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
        Schema::create('appointment_transaction', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('appointments')->onDelete('cascade');
            $table->string('txn_id')->nullable();
            $table->string('discount_type')->nullable();
            $table->double('discount_value')->default(0);
            $table->double('discount_amount')->default(0);
            $table->integer('coupon_id')->nullable();
            $table->longText('coupon')->nullable();
            $table->double('coupon_amount')->default(0);
            $table->longText('tax')->nullable();
            $table->double('total_tax_amount')->default(0);
            $table->double('total_amount')->default(0);
            $table->string('payment_type')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('request_token')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_transaction');
    }
};
