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
        Schema::table('orders', function (Blueprint $table) {
            // Add new fields for delivery cost calculation
            $table->text('order_contents')->nullable()->after('client_lng');
            $table->decimal('order_price', 10, 2)->nullable()->after('order_contents');
            $table->decimal('distance_km', 8, 2)->nullable()->after('vehicle_type');
            $table->decimal('delivery_cost', 10, 2)->nullable()->after('distance_km');
        });

        // Update vehicle_type enum to include 'pickup'
        DB::statement("ALTER TABLE orders MODIFY COLUMN vehicle_type ENUM('bike', 'car', 'pickup')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_contents', 'order_price', 'distance_km', 'delivery_cost']);
        });

        // Revert vehicle_type enum back to original
        DB::statement("ALTER TABLE orders MODIFY COLUMN vehicle_type ENUM('bike', 'car')");
    }
};
