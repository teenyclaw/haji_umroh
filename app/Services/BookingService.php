<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\CommissionType;
use App\Enums\InvoiceStatus;
use App\Enums\JamaahStatus;
use App\Models\Agent;
use App\Models\Booking;
use App\Models\Commission;
use App\Models\Invoice;
use App\Models\Jamaah;
use App\Models\Package;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    /**
     * @param  array<int, array{jamaah_id:int, room_type:string}>  $participants
     */
    public function create(Package $package, array $participants, ?int $agentId, ?string $notes = null): Booking
    {
        return DB::transaction(function () use ($package, $participants, $agentId, $notes) {
            $booking = Booking::create([
                'package_id' => $package->id,
                'agent_id' => $agentId,
                'code' => $this->generateCode('BK'),
                'booking_date' => now()->toDateString(),
                'status' => BookingStatus::Confirmed,
                'notes' => $notes,
                'total_amount' => 0,
            ]);

            $total = 0;

            foreach ($participants as $participant) {
                $price = $package->priceFor($participant['room_type']);
                $total += $price;

                $booking->jamaahPivot()->create([
                    'jamaah_id' => $participant['jamaah_id'],
                    'room_type' => $participant['room_type'],
                    'price' => $price,
                ]);

                Jamaah::whereKey($participant['jamaah_id'])->update(['status' => JamaahStatus::Booking]);
            }

            $booking->update(['total_amount' => $total]);

            Invoice::create([
                'booking_id' => $booking->id,
                'number' => $this->generateCode('INV'),
                'issued_date' => now()->toDateString(),
                'due_date' => $package->departure_date ?? now()->addMonths(2)->toDateString(),
                'total_amount' => $total,
                'paid_amount' => 0,
                'status' => InvoiceStatus::Unpaid,
            ]);

            if ($agentId) {
                $this->createCommission($booking, $agentId, $package, count($participants), $total);
            }

            log_activity('booking.created', "Booking {$booking->code} dibuat", $booking);

            return $booking->load('invoice');
        });
    }

    protected function createCommission(Booking $booking, int $agentId, Package $package, int $count, float $total): void
    {
        $agent = Agent::find($agentId);

        if (! $agent) {
            return;
        }

        $amount = $agent->commission_type === CommissionType::Percentage
            ? $total * ((float) $agent->commission_value / 100)
            : (float) $agent->commission_value * $count;

        Commission::create([
            'agent_id' => $agentId,
            'booking_id' => $booking->id,
            'amount' => $amount,
            'status' => 'pending',
        ]);
    }

    public function generateCode(string $prefix): string
    {
        return $prefix . '-' . now()->format('ymd') . '-' . strtoupper(Str::random(4));
    }
}
