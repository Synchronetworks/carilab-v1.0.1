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
        Schema::create('packagemanagements', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('vendor_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('lab_id')->nullable()->constrained('labs')->onDelete('cascade');
            
            // Basic information
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->double('price', 10, 2)->default(0);
            
            // Dates
            $table->date('start_at');
            $table->date('end_at');
            
            // Discount related
            $table->boolean('is_discount')->default(0);
            $table->string('discount_type')->nullable();
            $table->double('discount_price', 10, 2)->nullable();
            
            // Status
            $table->boolean('status')->default(1);
            $table->boolean('is_featured')->default(0);
            $table->boolean('is_home_collection_available')->default(0);
            $table->integer('parent_id')->unsigned()->nullable();
            // Audit columns
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
        Schema::dropIfExists('packagemanagements');
    }
};
