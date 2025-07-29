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
        Schema::create('catlogmanagements', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code')->nullable();
            $table->longText('type')->nullable();
            $table->longText('equipment')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->foreignId('vendor_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('lab_id')->nullable()->constrained('labs')->onDelete('cascade');
            $table->double('price', 10, 2)->default(0);
            $table->time('duration')->nullable(); // Duration in minutes
            $table->time('test_report_time')->nullable();
            $table->longText('instructions')->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('is_home_collection_available')->default(0);
            $table->longText('additional_notes')->nullable();
            $table->longText('restrictions')->nullable();
            $table->boolean('is_featured')->default(0);
            $table->integer('parent_id')->unsigned()->nullable();

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
        Schema::dropIfExists('catlogmanagements');
    }
};
