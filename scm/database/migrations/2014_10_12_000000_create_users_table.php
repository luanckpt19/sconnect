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
            $table->id();
			$table->string('staff_code')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('google_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');            
            $table->string('given_name')->nullable();
            $table->string('family_name')->nullable();
            $table->string('picture')->nullable();
            $table->string('user_token')->nullable();
            $table->string('phone', 15)->nullable();
            $table->tinyInteger('gender', false, true)->default(1);
            $table->tinyInteger('status', false, true)->default(1);
            $table->string('permission', 30)->nullable();
            $table->integer('department_id', false, true)->default(0);
            $table->integer('position', false, true)->default(0);
            
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
