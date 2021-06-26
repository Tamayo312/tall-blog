<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Add columns for each of the fields defined in our Post model.
         */
        Schema::create('posts', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->string('category');
            $table->string('excerpt');
            $table->longText('body');
            $table->string('title');
            $table->boolean('is_published')->default(false);
            $table->string('featured_image');
            $table->dateTime('published_date');
            $table->foreignId('user_id')->constrained(); // Foreing Key that references the users table
            $table->timestamps(); // Automatic creation of created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
