<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserKomisiHistory extends Model
{   
    // KOMISI HISTORY, USER 
    use HasFactory;
    protected $table = 'user_komisi_history';
    protected $guarded = [];
    public function komisi_affiliate_id(){
        return $this->belongsTo(User::class,  "affiliate_id");
    }
    public function komisi_affiliator_id(){
        return $this->belongsTo(User::class, "affiliator_id");
    }
}
