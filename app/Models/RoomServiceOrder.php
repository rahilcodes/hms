<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomServiceOrder extends Model
{
    protected $fillable = ['booking_id', 'items', 'total_amount', 'status', 'notes', 'type', 'kot_number'];

    protected $casts = [
        'items' => 'array',
        'total_amount' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::created(function ($order) {
            // Daily KOT sequence for dining orders (resets each day)
            if ($order->type !== 'service' && !$order->kot_number) {
                $order->updateQuietly([
                    'kot_number' => (int) (static::whereDate('created_at', now()->toDateString())
                        ->where('id', '!=', $order->id)
                        ->max('kot_number') ?? 0) + 1,
                ]);
            }

            $items = is_string($order->items) ? json_decode($order->items, true) : $order->items;
            if (is_array($items)) {
                foreach ($items as $item) {
                    \App\Models\FolioItem::create([
                        'hotel_id' => $order->booking->hotel_id,
                        'booking_id' => $order->booking_id,
                        'type' => 'service',
                        'description' => "Order #" . (5000 + $order->id) . ": " . ($item['name'] ?? 'Service'),
                        'amount' => ($item['price'] ?? 0) * ($item['qty'] ?? 1),
                        'posted_at' => now(),
                        'metadata' => [
                            'source' => 'room_service_order',
                            'order_type' => $order->type ?? 'dining',
                            'order_id' => $order->id,
                            'service_id' => $item['id'] ?? null
                        ]
                    ]);
                }
            }
        });
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
