<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use App\Models\Client;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables;
use Filament\Resources\Pages\ListRecords;

class ViewClient extends Page
{
    protected static string $resource = ClientResource::class;

    protected static string $view = 'filament.resources.client-resource.pages.view-client';

    public $record;
    public function mount($record)
    {

        $this->record = Client::with('campaigns')->find($record);
    }

    protected function getTableColumns(): array
    {

        return [
            Tables\Columns\TextColumn::make('tg_username')
                ->url(fn (Client $record): string => "https://t.me/$record->tg_username")
                ->openUrlInNewTab()
                ->label('Username'),
            Tables\Columns\TextColumn::make('geo')->label('Geo'),
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
