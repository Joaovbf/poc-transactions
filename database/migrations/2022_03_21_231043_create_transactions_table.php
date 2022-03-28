<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedFloat('amount');
            $table->bigInteger('payee_wallet_id')->unsigned();
            $table->bigInteger('payer_wallet_id')->unsigned();
            $table->foreign('payee_wallet_id')->references("id")->on("wallets");
            $table->foreign('payer_wallet_id')->references("id")->on("wallets");
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
        Schema::dropIfExists('transactions');
    }
}
