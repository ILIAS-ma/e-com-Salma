<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->string('size', 10);
            $table->string('color_name');
            $table->string('color_code', 20)->comment('hex code used to render the color swatch');
            $table->decimal('price_override', 10, 2)->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'size', 'color_name'], 'unique_variant_combination');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
