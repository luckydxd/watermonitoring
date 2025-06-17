<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name_app');
            $table->text('desc')->nullable();
            $table->string('logo')->nullable();
            $table->string('secondary_logo')->nullable();
            $table->string('no_contact')->nullable();
            $table->string('email')->nullable();
            $table->string('instagram')->nullable();
            $table->text('alamat')->nullable();
            $table->string('gmap_coordinat')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('app_settings');
    }
};
