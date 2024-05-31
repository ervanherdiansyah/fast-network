<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAlamat extends Model
{
    use HasFactory;
    protected $guarded = [
        'id',
    ];


    public function users()
    {
        return $this->belongsTo(User::class, "user_id");
    }
    public function order()
    {
        return $this->hasMany(Order::class, "alamat_id");
    }
    public function orderAlamatPengambilan()
    {
        return $this->hasMany(Order::class, "alamat_pengambilan_paket_id");
    }

    public function provinsi()
    {
        return $this->belongsTo(Province::class,  "provinsi_id");
    }

    public function kota()
    {
        return $this->belongsTo(City::class,  "kota_id");
    }
}
