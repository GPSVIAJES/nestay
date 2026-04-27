<?php

namespace App\Services\RateHawk\MockData;

/**
 * Simulates the ETG response for:
 *   POST /api/b2b/v3/hotel/order/booking/form/
 *
 * This step creates the booking on ETG's side and links it to
 * the partner's partner_order_id.
 *
 * On success: status = 'ok'
 * Frontend/backend should then proceed to call /booking/finish/.
 */
class BookingFormResult
{
    public static function get(string $partnerOrderId): array
    {
        return [
            'status' => 'ok',
            'data'   => [
                'partner_order_id' => $partnerOrderId,
                // ETG confirms the booking form was created on their side.
                // The actual booking is initiated in the /booking/finish/ step.
            ],
        ];
    }
}
