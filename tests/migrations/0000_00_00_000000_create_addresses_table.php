<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            $table->string('driver');
            $table->string('identifier');
            $table->unique(['driver', 'identifier']);

            $table->string('label')->nullable();
            $table->string('street')->nullable();
            $table->string('house_number')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('state')->nullable();
            $table->string('county')->nullable();
            $table->string('country', 2);

            $table->float('lat', 10, 6)->nullable();
            $table->float('lng', 10, 6)->nullable();

            $table->timestamps();
        });
    }
}
