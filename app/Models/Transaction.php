<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'category', 'est_weight', 'actual_weight',
        'method', 'status', 'reject_reason', 'total_price',
        'photo', 'location_lat', 'location_lng',
        'dropoff_location', 'pickup_address', 'pickup_datetime',
    ];

    protected function casts(): array
    {
        return [
            'pickup_datetime' => 'datetime',
            'est_weight' => 'decimal:2',
            'actual_weight' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'pending' => ['label' => 'Dikirim', 'class' => 'bg-yellow-100 text-yellow-700 border-yellow-200'],
            'weighing' => ['label' => 'Ditimbang', 'class' => 'bg-blue-100 text-blue-700 border-blue-200'],
            'complete' => ['label' => 'Selesai', 'class' => 'bg-green-100 text-green-700 border-green-200'],
            'rejected' => ['label' => 'Ditolak', 'class' => 'bg-red-100 text-red-700 border-red-200'],
            default => ['label' => $this->status, 'class' => 'bg-gray-100 text-gray-700 border-gray-200'],
        };
    }

    public function getMethodBadgeAttribute(): string
    {
        return $this->method === 'Pick-up'
            ? 'bg-purple-100 text-purple-700'
            : 'bg-blue-100 text-blue-700';
    }
}
