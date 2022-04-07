<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use App\Models\Client;
use Filament\Resources\Pages\ListRecords;
use App\Models\Country;
use App\services\ClientService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use SergiX44\Nutgram\Nutgram;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;
    

    protected function getTableColumns(): array
    {



        return [
            TextColumn::make('tg_username')
                ->url(fn (Client $record): string => "https://t.me/$record->tg_username")
                ->openUrlInNewTab()
                ->label('Username'),
            TextColumn::make('geo')->label('Geo'),
            BadgeColumn::make('prime')
                ->colors([
                    'danger' => fn ($state): bool => $state == false,
                    'success' => fn ($state): bool => $state == true,
                ])->enum([
                    false => "No",
                    true => "Yes",
                ]),
            BadgeColumn::make('status')
                ->colors([
                    'danger' => fn ($state): bool => $state == 3,
                    'success' => fn ($state): bool => $state == 2,
                    'primary' => fn ($state): bool => $state == 1,
                ])->enum([
                    1 => "Pending",
                    2 => "Approved",
                    3 => "Denied"
                ]),
            TextColumn::make('created_at')->label('Joined')->date(),
            TagsColumn::make('interestes')

        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            BulkAction::make('Approve')
                ->action('approved')
                ->requiresConfirmation()
                ->color('success')
                ->icon('heroicon-o-check'),
            BulkAction::make('deny')
                ->action('deny')
                ->requiresConfirmation()
                ->color('danger')
                ->icon('heroicon-o-check'),
            BulkAction::make('delete')
                ->action(fn (Collection $records) => $records->each->delete())
                ->deselectRecordsAfterCompletion()
                ->requiresConfirmation()
                ->color('danger')
                ->icon('heroicon-o-trash')

        ];
    }
    protected function getTableFilters(): array
    {
        $geo = [];
        foreach (Country::all()->pluck('name') as $ctry) {

            $geo[$ctry] = $ctry;
        }
        return [
            Filter::make('prime')->label('Prime'),
            SelectFilter::make('status')
                ->options([
                    1 => 'Pending',
                    2 => 'Approved',
                    3 => 'Denied',
                ])
                ->column('status'),
            SelectFilter::make('geo')
                ->options($geo)
                ->column('geo')

        ];
    }

    public function approved(Nutgram $bot, Collection $records)
    {
        foreach ($records as $record) {
            $record->update(['status' => 2]);
            ClientService::approved($bot, $record);
        }
    }

    public function deny(Nutgram $bot, Collection $records)
    {
        foreach ($records as $record) {
            $record->update(['status' => 3]);
            ClientService::denied($bot, $record);
        }
    }
}
