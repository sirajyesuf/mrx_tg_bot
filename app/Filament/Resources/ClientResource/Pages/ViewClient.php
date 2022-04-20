<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use App\Models\Client;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\ViewRecord;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;
    protected static ?string $title = 'Claims';
    protected static string $view = 'filament.resources.client-resource.pages.view-client';
}
