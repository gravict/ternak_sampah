<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = ['name', 'cost_points', 'icon'];

    public function userVouchers()
    {
        return $this->hasMany(UserVoucher::class);
    }
}
