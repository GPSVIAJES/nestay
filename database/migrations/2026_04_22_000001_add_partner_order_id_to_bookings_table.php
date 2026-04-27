<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add partner_order_id to bookings table.
     *
     * This is the ID we generate and send to ETG in the booking/form step.
     * ETG uses it to link their booking to our system.
     * We use it to poll /booking/finish/status/ and cancel orders.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('partner_order_id')->nullable()->unique()->after('ratehawk_order_id');
            $table->index('partner_order_id');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['partner_order_id']);
            $table->dropColumn('partner_order_id');
        });
    }
};
