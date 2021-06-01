<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_product', function (Blueprint $table) {
            $table->dropForeign('order_product_option_id_foreign');
            $table->dropColumn('option_id');
        });

        Schema::create('order_product_option', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_product_id');
            $table->unsignedInteger('option_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('order_product_id')
                ->references('id')
                ->on('order_product')
                ->onDelete('cascade');
            $table->foreign('option_id')
                ->references('id')
                ->on('options')
                ->onDelete('cascade');
        });

        Schema::create('attributes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')
                ->references('id')->on('companies')
                ->onDelete('cascade');
        });

        Schema::table('options', function (Blueprint $table) {
            $table->unsignedInteger('attribute_id')
                ->nullable()
                ->after('company_id');
            $table->integer('stock')
                ->default(0)
                ->after('name');
            $table->foreign('attribute_id')
                ->references('id')
                ->on('attributes')
                ->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_product_option');
        Schema::table('order_product', function (Blueprint $table) {
            $table
                ->unsignedInteger('option_id')
                ->nullable()
                ->after('id');
            $table->foreign('option_id')
                ->references('id')
                ->on('options')
                ->onDelete('cascade');
        });
        Schema::table('options', function (Blueprint $table) {
            $table->dropForeign('options_attribute_id_foreign');
            $table->dropColumn('attribute_id');
            $table->dropColumn('stock');
        });
        Schema::dropIfExists('attributes');
    }
}
