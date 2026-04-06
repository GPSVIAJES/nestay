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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // RateHawk identifiers
            $table->string('ratehawk_order_id')->nullable()->unique();
            $table->string('book_hash')->nullable();

            // Hotel info (snapshot at booking time)
            $table->string('hotel_id');
            $table->string('hotel_name');
            $table->string('hotel_address')->nullable();
            $table->string('hotel_city')->nullable();
            $table->string('hotel_country')->nullable();
            $table->string('hotel_stars')->nullable();
            $table->string('hotel_image')->nullable();

            // Stay details
            $table->date('check_in');
            $table->date('check_out');
            $table->unsignedTinyInteger('guests')->default(1);
            $table->unsignedTinyInteger('rooms')->default(1);
            $table->json('rooms_data')->nullable(); // Rate/room details from API

            // Pricing
            $table->decimal('total_price', 10, 2);
            $table->string('currency', 3)->default('USD');

            // Guest info
            $table->string('guest_first_name');
            $table->string('guest_last_name');
            $table->string('guest_email');
            $table->string('guest_phone')->nullable();

            // Booking status: pending, confirmed, cancelled, failed
            $table->string('status')->default('pending');
            $table->text('cancellation_policy')->nullable();

            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->index('check_in');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
