<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function userAlamat()
    {
        return $this->hasMany(UserAlamat::class,  "kota_id");
    }
}
