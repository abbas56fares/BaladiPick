<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert default delivery pricing settings
        DB::table('settings')->insert([
            ['key' => 'bike_base_fee', 'value' => '2.00', 'description' => 'Base fee for bike delivery (USD)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'bike_rate_per_km', 'value' => '0.30', 'description' => 'Rate per kilometer for bike (USD)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'bike_min_charge', 'value' => '3.00', 'description' => 'Minimum charge for bike delivery (USD)', 'created_at' => now(), 'updated_at' => now()],
            
            ['key' => 'car_base_fee', 'value' => '3.50', 'description' => 'Base fee for car delivery (USD)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'car_rate_per_km', 'value' => '0.60', 'description' => 'Rate per kilometer for car (USD)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'car_min_charge', 'value' => '4.00', 'description' => 'Minimum charge for car delivery (USD)', 'created_at' => now(), 'updated_at' => now()],
            
            ['key' => 'pickup_base_fee', 'value' => '10.00', 'description' => 'Base fee for pickup/big car delivery (USD)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'pickup_rate_per_km', 'value' => '1.25', 'description' => 'Rate per kilometer for pickup (USD)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'pickup_min_charge', 'value' => '10.00', 'description' => 'Minimum charge for pickup delivery (USD)', 'created_at' => now(), 'updated_at' => now()],
            
            ['key' => 'fuel_adjustment', 'value' => '1.00', 'description' => 'Fuel price multiplier (1.00 = normal, 1.15 = 15% increase)', 'created_at' => now(), 'updated_at' => now()],
            
            ['key' => 'bike_max_distance', 'value' => '10', 'description' => 'Maximum distance for bike deliveries (km)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'car_max_distance', 'value' => '90', 'description' => 'Maximum distance for car deliveries (km)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'pickup_max_distance', 'value' => '90', 'description' => 'Maximum distance for pickup deliveries (km)', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
