<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'gm_geo' => 'array',
        'gm_interest' => 'array'
    ];

    public function countries()
    {
        return $this->belongsToMany(CampaignCountry::class);
    }
}
