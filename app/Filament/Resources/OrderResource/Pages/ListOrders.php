<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Client;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use App\Models\Order;
use App\services\ClientService;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\IconButtonAction;
use Filament\Forms\Components\Textarea;
use SergiX44\Nutgram\Nutgram;
class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getTableQuery(): Builder
    {
        return Order::query()->with(['client', 'campaign']);
    }
    protected function getTableColumns(): array
    {

        return [

            // TextColumn::make('client.name')->label('Client'),
            TextColumn::make('client.tg_username')
                ->url(fn (Order $record): string => "https://t.me/".$record->client->tg_username)
                ->openUrlInNewTab()
                ->label('Client'),
            TextColumn::make('campaign.gm_text')->html(),
            TextColumn::make('payment_method'),
            TagsColumn::make('payment_method')->separator(),
            TextColumn::make('information')->default(''),
            TextColumn::make('proof')->limit(10)
                ->url(fn (Order $record): string =>  asset($record->proof))
                ->openUrlInNewTab(),
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




        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            // ...
        ];
    }

    protected function getTableActions(): array
    {

        return [
            IconButtonAction::make('approve')
                ->action('approve')
                ->label('Approve')
                ->color('success')
                ->icon('heroicon-o-check')
                ->requiresConfirmation()
                ->hidden(fn (Order $record): bool => 1 == $record->status),
            IconButtonAction::make('deny')
                ->action('deny')
                ->label('Deny')
                ->color('danger')
                ->icon('heroicon-o-check')
                ->requiresConfirmation()
                ->form([
                    Textarea::make('deny_message')->label('message for client')
                        ->required()
                ])
                ->hidden(fn (Order $record): bool => 1 == $record->status),
        ];
    }
    protected function getTableFilters(): array
    {

        $clients=array();
        foreach(Client::all() as $client){
          $clients[$client->id] = $client['tg_username'];
        }
        return [
            SelectFilter::make('client')
            ->options($clients)
            ->column('client_id'),
            


        ];
    }


    public function approve(Nutgram $bot, $record)
    {

        $text = "your payment request approved.";
        $record->update([
            'status' => 2

        ]);
        ClientService::approve($bot, $text, $record->client->tg_user_id);
    }

    public function deny(Nutgram $bot, $record, $data)
    {



        $text = "your payment request denied.\nReason: " . $data['deny_message'];
        $record->update([
            'status' => 3,
            'deny_message' => $data['deny_message']

        ]);
        ClientService::deny($bot, $text, $record->client->tg_user_id);
    }
}
