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
        return $this->belongsToMany(Campaign::class)->using(CampaignClient::class)
            ->as('claim')
            ->withPivot('product_id', 'tg_message_id', 'status', 'claim_target_chat_id')
            ->withTimestamps();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
