<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;

class Campaign extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'gm_geo' => 'array',
        'gm_interest' => 'array',
        'payment_methods' => 'array'
    ];

    public function clients()
    {

        return $this->belongsToMany(Client::class)->using(CampaignClient::class)
        ->as('claim')
            ->withPivot('product_id', 'tg_message_id', 'status', 'claim_target_chat_id')
            ->withTimestamps();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
            
    }


    // public function bmApplyBtnActiveDuration(): Attribute
    // {
    //     return new Attribute(
    //         get: fn ($value, $attributes) => Carbon::parse($value)->diffAsCarbonInterval($attributes['updated_at']),
    //     );
    // }
}
