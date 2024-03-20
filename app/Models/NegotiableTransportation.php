<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NegotiableTransportation extends Model
{
    use HasFactory;

    protected $table = 'negotiable_transportation';

    protected $fillable = [
        'status', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
