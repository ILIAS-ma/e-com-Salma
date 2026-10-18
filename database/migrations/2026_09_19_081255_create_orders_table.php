<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('city_id')->constrained('cities');
            $table->foreignId('shipping_zone_id')->constrained('shipping_zones');

            // guest-friendly contact & delivery info
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->text('address');
            $table->string('postal_code')->nullable();
            $table->text('notes')->nullable();

            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_cost', 8, 2);
            $table->decimal('discount', 8, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('currency', 3)->default('MAD');

            $table->enum('payment_method', ['cmi', 'stripe', 'paypal', 'cod'])->default('cod');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');

            $table->enum('status', [
                'en_attente', 'confirmee', 'en_preparation', 'en_livraison', 'livree', 'annulee',
            ])->default('en_attente');

            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
