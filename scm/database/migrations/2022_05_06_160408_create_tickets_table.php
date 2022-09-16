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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('video_id')->default(0);
            $table->string('title');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->unsignedInteger('budget')->default(0);
            $table->unsignedTinyInteger('gender')->default(0);
            $table->string('age')->default('');
            $table->text('location')->nullable();
            $table->unsignedTinyInteger('kind')->default(0);
            $table->string('keyword', 255);
            $table->text('note')->nullable();
            /*
            0 = QTK draft, 
            1 = QTK sent to MKT
            2 = MKT Review
            3 = Comment
            4 = Running
            5 = Paused
            6 = Finished
            */
            $table->unsignedTinyInteger('workflow_position')->default(0);  
            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedInteger('mkt_user_id')->default(0);
            $table->string('campaign_id');
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
        Schema::dropIfExists('tickets');
    }
};
