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

        return $this->belongsToMany(CampaignClient::class);
    }


    // public function bmApplyBtnActiveDuration(): Attribute
    // {
    //     return new Attribute(
    //         get: fn ($value, $attributes) => Carbon::parse($value)->diffAsCarbonInterval($attributes['updated_at']),
    //     );
    // }
}
