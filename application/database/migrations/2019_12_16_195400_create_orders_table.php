<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('payment_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('user_address_id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('payment_type_id');
            $table->unsignedInteger('payment_status_id');
            $table->string('external_type')->nullable();
            $table->unsignedInteger('external_id')->nullable();
            $table->double('shipment')->default(0);
            $table->string('shipment_option')->nullable();
            $table->double('discount')->default(0);
            $table->double('amount_without_shipment')->default(0);
            $table->double('amount_without_discount')->default(0);
            $table->double('amount')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table
                ->foreign('user_address_id')
                ->references('id')
                ->on('user_addresses')
                ->onDelete('no action');
            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('no action');
            $table
                ->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('no action');
            $table
                ->foreign('payment_type_id')
                ->references('id')
                ->on('payment_types')
                ->onDelete('no action');
            $table
                ->foreign('payment_status_id')
                ->references('id')
                ->on('payment_statuses')
                ->onDelete('no action');
        });

        Schema::create('order_product', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('product_id');
            $table->integer('quantity')->default(0);
            $table->double('price')->default(0);
            $table->double('amount')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('no action');
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
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
        Schema::dropIfExists('order_product');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('payment_types');
        Schema::dropIfExists('payment_statuses');
    }
}
