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
        Schema::create('prescription_test_mapping', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained('prescriptions')->onDelete('cascade');
            $table->integer('test_id')->nullable();
            $table->double('price', 10, 2);
            // Dates
            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();

            $table->boolean('is_discount')->default(0);
            $table->string('discount_type')->nullable();
            $table->double('discount_price', 10, 2)->nullable();
            $table->string('type')->nullable();
            
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_package_mapping');
    }
};
