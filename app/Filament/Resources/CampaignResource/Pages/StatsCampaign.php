<?php

namespace App\Filament\Resources\CampaignResource\Pages;

use App\Filament\Resources\CampaignResource;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\ViewRecord;

class StatsCampaign extends ViewRecord
{
    protected static string $resource = CampaignResource::class;
    protected static ?string $title = 'campaign statistics';
    protected static string $view = 'filament.resources.campaign-resource.pages.stats-campaign';
}
