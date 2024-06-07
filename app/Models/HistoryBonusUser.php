<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryBonusUser extends Model
{
    use HasFactory;
    protected $table = 'user_bonus_history';
    protected $guarded = [
        'id',
    ];
}
