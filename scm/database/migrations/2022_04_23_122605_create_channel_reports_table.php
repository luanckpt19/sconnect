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
        Schema::create('channel_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('channel_id')->default(0);
            $table->string('date', 20); // yyyy-mm-dd
            $table->unsignedInteger('video_count')->default(0);
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('subcriber_count')->default(0); // subscriber / follow / like
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
        Schema::dropIfExists('channel_reports');
    }
};
