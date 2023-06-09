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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id(); //notif id
            $table->text('content'); //notif content
            $table->timestamps();  //notif creation time
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade'); //what triggered notif
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); //who recieves notif
            $table->foreign('friend_id')->references('id')->on('users')->onDelete('cascade');  //who triggered notif
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
