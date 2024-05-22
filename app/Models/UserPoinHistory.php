<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPoinHistory extends Model
{
    use HasFactory;
    protected $table = 'user_point_history';
    protected $guarded = [];
}
