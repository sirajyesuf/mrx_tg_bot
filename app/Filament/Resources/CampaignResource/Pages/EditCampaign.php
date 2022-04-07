<?php

namespace App\Filament\Resources\CampaignResource\Pages;

use App\Filament\Resources\CampaignResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\Fieldset;
use Filament\Forms;
use Filament\Resources\Form;
use App\Models\Country;
use App\Models\Interest;

class EditCampaign extends EditRecord
{
    protected static string $resource = CampaignResource::class;
}
