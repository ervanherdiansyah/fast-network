<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function users()
    {
        return $this->belongsTo(User::class,  "user_id");
    }
    public function paket()
    {
        return $this->belongsTo(Paket::class,  "paket_id");
    }
    public function orderDetail()
    {
        return $this->hasMany(OrderDetail::class,  "order_id");
    }

    public function userAlamat()
    {
        return $this->belongsTo(UserAlamat::class,  "alamat_id");
    }
    public function alamatPengiriman()
    {
        return $this->belongsTo(UserAlamat::class,  "alamat_pengambilan_paket_id");
    }
}
