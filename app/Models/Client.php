<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'interestes' => 'array'
    ];

    public function campaigns()
    {
        return $this->belongsToMany(CampaignClient::class);
    }
}
