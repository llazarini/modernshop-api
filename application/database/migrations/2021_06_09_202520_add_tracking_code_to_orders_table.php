<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrackingCodeToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')
                ->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('shipping_options', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('shipping_company_id');
            $table->string('name');
            $table->text('description')
                ->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table
                ->foreign('shipping_company_id')
                ->references('id')
                ->on('shipping_companies')
                ->onDelete('no action');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('shipping_option_id')
                ->after('payment_status_id')
                ->nullable();
            $table->dropColumn('shipment_option');
            $table->string('tracking_code')
                ->nullable()
                ->after('shipment_option');
            $table
                ->foreign('shipping_option_id')
                ->references('id')
                ->on('shipping_options')
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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_shipping_option_id_foreign');
            $table->dropColumn('shipping_option_id');
            $table->dropColumn('tracking_code');
            $table->string('shipment_option')
                ->nullable()
                ->after('shipment');
        });
        Schema::dropIfExists('shipping_options');
        Schema::dropIfExists('shipping_companies');
    }
}
