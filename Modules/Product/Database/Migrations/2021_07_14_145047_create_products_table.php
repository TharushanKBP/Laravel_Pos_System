<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('product_name');
            $table->string('product_code')->unique()->nullable();
            $table->string('product_barcode_symbology')->nullable();
            $table->decimal('product_quantity', 8, 2); // Changed to decimal with precision 8 and scale 2
            $table->decimal('product_cost', 15, 2); // Changed to decimal with higher precision and scale
            $table->decimal('product_price', 15, 2); // Changed to decimal with higher precision and scale
            $table->string('product_unit')->nullable();
            $table->integer('product_stock_alert');
            $table->decimal('product_order_tax', 5, 2)->nullable(); // Changed to decimal with precision 5 and scale 2
            $table->tinyInteger('product_tax_type')->nullable();
            $table->text('product_note')->nullable();
            $table->string('product_image')->nullable(); // Added product_image column
            $table->foreign('category_id')->references('id')->on('categories')->restrictOnDelete();
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
        Schema::dropIfExists('products');
    }
}
