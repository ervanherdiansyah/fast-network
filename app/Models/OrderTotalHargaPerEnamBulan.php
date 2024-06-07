<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTotalHargaPerEnamBulan extends Model
{
    use HasFactory;

    protected $table = 'order_total_enam_bulan';
    protected $guarded = [];

    public function user()
    {
        // affiliate_id -> user_id
        return $this->belongsTo(User::class, "user_id");
    }
}
