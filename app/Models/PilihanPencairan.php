<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PilihanPencairan extends Model
{
    use HasFactory;
    protected $table = 'pilihan_cepat_pencairan';
    protected $guarded = [];
}
