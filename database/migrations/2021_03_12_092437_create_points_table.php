<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->double('amount')->nullable();
            $table->string('noID')->nullable();
            $table->integer('type')->nullable()->default(1);
            $table->double('qnt')->default(0);
            $table->string('note')->nullable();
            $table->integer('status')->default(0)->nullable();
            $table->unsignedBigInteger('point_id')->nullable();
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
        Schema::dropIfExists('points');
    }
}
