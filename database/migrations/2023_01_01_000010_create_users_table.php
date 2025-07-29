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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('mobile')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('cascade');
            $table->foreignId('state_id')->nullable()->constrained('states')->onDelete('cascade');
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('cascade');
            $table->longText('address')->nullable();
            $table->string('login_type')->nullable();
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->tinyInteger('is_verify')->default(0);
            $table->tinyInteger('is_banned')->default(0);
            $table->tinyInteger('is_subscribe')->default(0);
            $table->tinyInteger('is_available')->nullable()->default('0')->comment('1- true , 0- false');
            $table->time('last_online_time')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->boolean('set_as_featured')->default(0);
            $table->timestamp('last_notification_seen')->nullable();
            $table->string('user_type')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->string('social_image')->nullable();
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
        Schema::dropIfExists('users');
    }
};
