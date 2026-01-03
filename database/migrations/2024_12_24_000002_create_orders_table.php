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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->foreignId('delivery_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('client_name');
            $table->string('client_phone');
            $table->decimal('client_lat', 10, 8);
            $table->decimal('client_lng', 11, 8);
            $table->decimal('shop_lat', 10, 8);
            $table->decimal('shop_lng', 11, 8);
            $table->text('order_contents')->nullable()->comment('Description of what is being delivered');
            $table->decimal('order_price', 10, 2)->default(0)->comment('Value of items being delivered');
            $table->decimal('distance_km', 8, 2)->nullable()->comment('Distance in kilometers');
            $table->enum('vehicle_type', ['bike', 'car', 'pickup'])->comment('Required vehicle type for this order');
            $table->decimal('delivery_cost', 10, 2)->default(0)->comment('Auto-calculated delivery fee');
            $table->decimal('profit', 10, 2)->comment('Shop profit/commission');
            $table->enum('status', ['available', 'pending', 'in_transit', 'delivered', 'cancelled'])->default('available');
            $table->string('qr_code')->nullable();
            $table->boolean('qr_verified')->default(false);
            $table->timestamp('qr_verified_at')->nullable();
            $table->string('delivery_otp')->nullable();
            $table->boolean('delivery_verified')->default(false);
            $table->timestamp('delivery_verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
