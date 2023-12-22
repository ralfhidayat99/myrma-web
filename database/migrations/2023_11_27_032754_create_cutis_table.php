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
        Schema::create('cutis', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('jenis_cuti');
            $table->string('tgl_cuti');
            $table->string('tgl_kembali');
            $table->string('keperluan');
            $table->string('pengganti');
            $table->boolean('approve');
            $table->string('decline_reason');
            $table->integer('approved_by');
            $table->integer('is_known');
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
        Schema::dropIfExists('cutis');
    }
};
