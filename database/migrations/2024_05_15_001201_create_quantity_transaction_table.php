<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quantity_transaction', function (Blueprint $table) {
            $table->id('change_quantity_id');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('user_id')->on('users')
            ->onDelete('cascade')->onUpdate('cascade');
            $table->string('remarks')->nullable();

            $table->timestamps();
        });

        Schema::create('change_quantity', function (Blueprint $table) {
            $table->id('alter_quantity_id');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('product_id')->on('products')
            ->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('change_quantity_id');
            $table->foreign('change_quantity_id')->references('change_quantity_id')->on('quantity_transaction')
            ->onDelete('cascade')->onUpdate('cascade');
            $table->double('error');

            $table->float('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quantity_transaction');

        Schema::dropIfExists('change_quantity');
    }
};
