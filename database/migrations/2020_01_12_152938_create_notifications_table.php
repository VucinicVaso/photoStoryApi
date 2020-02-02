<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('notification_for_id');
            $table->unsignedBigInteger('notification_from_id');
            $table->unsignedBigInteger('target');
            $table->string('type');
            $table->integer('status');
            $table->timestamps();

            $table->foreign('notification_for_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('notification_from_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('target')->references('id')->on('posts')->onDelete('cascade');
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
}
