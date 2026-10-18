<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('e.g. Casablanca-Rabat, Grandes villes, Zones périphériques');
            $table->text('description')->nullable();
            $table->decimal('rate', 8, 2)->comment('standard delivery cost in MAD');
            $table->decimal('cod_surcharge', 8, 2)->default(0)->comment('extra fee for cash on delivery, in MAD');
            $table->decimal('free_shipping_threshold', 10, 2)->nullable()->comment('order subtotal in MAD above which delivery is free');
            $table->unsignedInteger('estimated_days_min')->default(1);
            $table->unsignedInteger('estimated_days_max')->default(3);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_zones');
    }
};
