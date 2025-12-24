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
            $table->enum('vehicle_type', ['bike', 'car']);
            $table->decimal('profit', 10, 2);
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
