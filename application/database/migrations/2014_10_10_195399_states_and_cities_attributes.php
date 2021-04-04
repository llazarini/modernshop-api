<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StatesAndCitiesAttributes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('states', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug');
            $table->string("code");
            $table->string("iso");
            $table->bigInteger('population')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cities', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('state_id');
            $table->string('name');
            $table->string("slug");
            $table->double("income_per_capita")->nullable();
            $table->string("long")->nullable();
            $table->string("lat")->nullable();
            $table->string("population")->nullable();
            $table->string("status")->nullable();
            $table->string("iso_ddd")->nullable();
            $table->string("iso")->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('state_id')
                ->references('id')->on('states')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cities');
        Schema::dropIfExists('states');
    }
}
