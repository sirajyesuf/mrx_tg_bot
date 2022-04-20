<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Resources\Pages\Page;
use Filament\Pages;
use App\Models\Client;
use Filament\Tables;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ClaimHistory extends ListRecords
{
    protected static string $resource = ClientResource::class;

    public $record;

    protected static string $view = 'filament.resources.client-resource.pages.claim-history';

    // public function mount(): void
    // {
    //     $this->record = Client::find($this->record)->campaigns;
    //     // dd($this->record);



    // }

    protected function getActions(): array
    {
        return [
            Pages\Actions\ButtonAction::make('settings')->action('openSettingsModal'),
        ];
    }



    protected function getTableQuery(): Builder
    {
        return Client::with('campaigns');
    }

    protected function getTableColumns(): array
    {


        return [

            Tables\Columns\TextColumn::make('campaigns')->label('id'),



        ];
    }
}
