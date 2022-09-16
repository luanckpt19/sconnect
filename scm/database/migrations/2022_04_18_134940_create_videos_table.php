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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url')->unique();
            $table->string('thumbnail')->nullable();
            $table->integer('channel_id', false, true)->default(0);
            $table->integer('product_id', false, true)->default(0);
            $table->string('note', 512)->nullable();
            $table->text('description')->nullable();
            $table->integer('view_count', false, true)->default(0);
            $table->integer('like_count', false, true)->default(0);
            $table->integer('dislike_count', false, true)->default(0);
            $table->integer('share_count', false, true)->default(0);
            $table->timestamp('joined_date')->nullable();
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
        Schema::dropIfExists('videos');
    }
};
