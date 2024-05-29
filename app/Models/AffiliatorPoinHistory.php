<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliatorPoinHistory extends Model
{
    use HasFactory;
    protected $table = 'affiliator_poin_history'; 
    protected $guarded = [];

    public function komisipoin_affiliate_id(){
        return $this->belongsTo(User::class,  "affiliate_id");
    }
    public function komisipoin_affiliator_id(){
        return $this->belongsTo(User::class, "affiliator_id");
    }
}
