<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\ViewRecord;
use App\Models\Client;
use Filament\Forms\Components\TextInput;

class ViewClient extends ViewRecord
{

    protected static string $resource = ClientResource::class;

    // protected static string $view = 'filament.resources.client-resource.pages.view-client';
}
