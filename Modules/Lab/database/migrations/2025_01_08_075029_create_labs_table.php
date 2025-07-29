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
        Schema::create('labs', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('lab_code')->nullable();
            $table->text('description')->nullable();
            $table->integer('vendor_id')->nullable();
            
            // Contact Information
            $table->string('phone_number')->nullable()  ;
            $table->string('email')->nullable();
            // Address Information
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('state_id')->nullable();
            $table->integer('country_id')->nullable();
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Operating Hours
            $table->integer('time_slot')->nullable();
            // License Information
            $table->string('license_number')->nullable();
            $table->date('license_expiry_date')->nullable();
            
            // Accreditation Information
            $table->string('accreditation_type')->nullable();
            $table->date('accreditation_expiry_date')->nullable();
            
            // Business Information
            $table->string('tax_identification_number')->nullable();
            $table->text('payment_modes')->nullable(); // Stores payment modes as JSON 
            $table->text('payment_gateways')->nullable(); // Stores payment gateways as JSON 
            // Status
            $table->boolean('status')->default('1');
            
            // Audit Fields
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
            
            // Timestamps
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
        Schema::dropIfExists('labs');
    }
};
