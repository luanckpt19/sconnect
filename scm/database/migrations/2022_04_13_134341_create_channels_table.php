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
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url')->unique();
            $table->string('thumbnail')->nullable();
            $table->integer('department_id', false, true)->default(0);
            $table->integer('platform_id', false, true)->default(0);
            $table->integer('topic_id', false, true)->default(0);
            $table->integer('staff_manager_id', false, true)->default(0);
            $table->integer('channel_type_id', false, true)->default(0);
            $table->string('schedule')->nullable();
            $table->string('note', 512)->nullable();
            $table->text('description')->nullable();
            $table->timestamp('joined_date')->nullable();
            $table->bigInteger('views', false, true)->default(0);
            $table->integer('video_count', false, true)->default(0);
            $table->integer('subcriber', false, true)->default(0);
            $table->unsignedTinyInteger('status')->default(0);  // 0-Active; 1-Limit; 2-Suspended; ...
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
        Schema::dropIfExists('channels');
    }
};
