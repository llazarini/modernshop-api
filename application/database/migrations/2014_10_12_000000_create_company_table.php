<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('cpf_cnpj')->nullable();
            $table->string('company_segment')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('auto_transaction')->default(0);
            $table->boolean('module_online_store')->default(1);
            $table->boolean('module_products')->default(1);
            $table->boolean('module_services')->default(1);
            $table->boolean('module_projects')->default(1);
            $table->longText('pdf_top')->nullable();
            $table->longText('pdf_bottom')->nullable();
            $table->longText('pdf_first_page')->nullable();
            $table->longText('pdf_last_page')->nullable();
            $table->longText('pdf_default_invoice')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
