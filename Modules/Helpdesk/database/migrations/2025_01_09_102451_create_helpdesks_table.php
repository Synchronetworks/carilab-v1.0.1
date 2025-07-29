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
        Schema::create('help_desk', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->unsignedBigInteger('user_id');
            $table->string('email')->nullable();
            $table->string('contact_number', 255)->nullable();
            $table->string('mode')->nullable();
            $table->longText('description')->nullable();
            $table->string('status')->nullable()->default('0');
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('help_desk');
    }
};
