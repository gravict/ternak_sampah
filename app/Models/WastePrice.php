<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WastePrice extends Model
{
    protected $fillable = ['category', 'sub_category', 'price_per_unit', 'unit'];
}
