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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('pending');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('other_member_id')->nullable();
            $table->foreign('other_member_id')->references('id')->on('user_other_mapping')->onDelete('cascade');            
            $table->foreignId('vendor_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('lab_id')->nullable()->constrained('labs')->onDelete('cascade');
            $table->integer('test_id')->nullable();
            $table->string('test_type')->default('test_case');
            $table->integer('address_id')->nullable();
            $table->date('appointment_date')->nullable();
            $table->time('appointment_time')->nullable();
            $table->double('amount')->default(0);
            $table->double('test_discount_amount')->default(0);
            $table->string('collection_type')->default('lab');
            $table->double('total_amount')->default(0);
            $table->string('submission_status')->default('pending');
            $table->string('test_case_status')->nullable();
            $table->integer('rejected_id')->nullable();
            $table->boolean('by_suggestion')->default('0');
            $table->longText('cancellation_reason')->nullable();
            $table->longText('symptoms')->nullable();
            $table->string('reschedule_reason')->nullable();

            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
    }
};
