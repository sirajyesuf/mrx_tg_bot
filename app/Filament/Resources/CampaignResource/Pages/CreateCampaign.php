<?php

namespace App\Filament\Resources\CampaignResource\Pages;

use App\Filament\Resources\CampaignResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Forms\Components\Fieldset;
use App\Models\Country;
use App\Models\Interest;
use Carbon\Carbon;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class CreateCampaign extends CreateRecord
{
    protected static string $resource = CampaignResource::class;
}
