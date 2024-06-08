<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('reference');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name');
            $table->integer('tax_percentage')->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0); // Changed to decimal with higher precision and scale
            $table->integer('discount_percentage')->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0); // Changed to decimal with higher precision and scale
            $table->decimal('shipping_amount', 15, 2)->default(0); // Changed to decimal with higher precision and scale
            $table->decimal('total_amount', 15, 2); // Changed to decimal with higher precision and scale
            $table->decimal('paid_amount', 15, 2); // Changed to decimal with higher precision and scale
            $table->decimal('due_amount', 15, 2); // Changed to decimal with higher precision and scale
            $table->string('status');
            $table->string('payment_status');
            $table->string('payment_method');
            $table->text('note')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
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
        Schema::dropIfExists('sales');
    }
}
