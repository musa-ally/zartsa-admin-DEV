<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_charges', function (Blueprint $table) {
            $table->id();
            $table->integer('service_id');
            $table->double('service_charge',10,2);
            $table->double('service_charge_discount',10,2);
            $table->string('service_charge_vat',5);
            $table->string('payment_grace_period',5); //Number Of Days
            $table->smallInteger('service_approval')->default(0); //Default Inactive
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
        Schema::dropIfExists('service_charges');
    }
}
