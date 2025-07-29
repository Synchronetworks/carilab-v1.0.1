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
        Schema::create('cash_payment_histories', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys should be unsignedBigInteger before making them foreign
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->foreign('transaction_id')->references('id')->on('appointment_transaction')->onDelete('cascade');
            
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('cascade');
        
            $table->string('action')->nullable();
            $table->string('text')->nullable();
            $table->string('type')->nullable();
        
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
        
            $table->unsignedBigInteger('receiver_id')->nullable();
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
        
            $table->dateTime('datetime')->nullable();
            $table->string('status')->nullable();
            $table->double('total_amount')->nullable()->default(0);
            $table->integer('parent_id')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_payment_histories');
    }
};
