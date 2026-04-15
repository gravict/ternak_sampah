<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserVoucher extends Model
{
    protected $fillable = ['user_id', 'voucher_id', 'claimed_at'];

    protected function casts(): array
    {
        return ['claimed_at' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
}
