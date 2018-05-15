<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKomentarKegiatansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('komentar_kegiatans', function (Blueprint $table) {
            $table->integer('userid')->unsigned();
            $table->integer('kgtid')->unsigned();
            $table->text('komentar');
            $table->timestamps();
        });

        Schema::table('komentar_kegiatans', function ($table){
            $table->foreign('userid')->references('id')->on('users') ->onDelete('cascade');
            $table->foreign('kgtid')->references('id')->on('kegiatans') ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('komentar_kegiatans');
    }
}
