<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Resources\Pages\Page;
use Filament\Pages;
use App\Models\Client;
use Filament\Tables;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ClaimHistory extends Page
{
    protected static string $resource = ClientResource::class;

    protected static string $view = 'filament.resources.client-resource.pages.claim-history';

    protected function getActions(): array
    {
        return [
            Pages\Actions\ButtonAction::make('settings')->action('openSettingsModal'),
        ];
    }



    protected function getTableQuery(): Builder
    {

        dd($this->record->campaigns);
        // return Client::query()->campaigns;
    }


    public function openSettingsModal(): void
    {
        $this->dispatchBrowserEvent('open-settings-modal');
    }
    protected function getTableColumns(): array
    {


        return [
            Tables\Columns\TextColumn::make('tg_username')
                ->url(fn (Client $record): string => "https://t.me/$record->tg_username")
                ->openUrlInNewTab()
                ->label('Username'),
            Tables\Columns\TextColumn::make('campaign.claim.id')->label('Geo'),
            Tables\Columns\BadgeColumn::make('prime')
                ->colors([
                    'danger' => fn ($state): bool => $state == false,
                    'success' => fn ($state): bool => $state == true,
                ])->enum([
                    false => "No",
                    true => "Yes",
                ]),
            Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'danger' => fn ($state): bool => $state == 3,
                    'success' => fn ($state): bool => $state == 2,
                    'primary' => fn ($state): bool => $state == 1,
                ])->enum([
                    1 => "Pending",
                    2 => "Approved",
                    3 => "Denied"
                ]),
            Tables\Columns\TextColumn::make('created_at')->label('Joined')->date(),
            Tables\Columns\TagsColumn::make('interestes'),
            Tables\Columns\BadgeColumn::make('orders_count')->counts('orders')->label('Orders')


        ];
    }
}
