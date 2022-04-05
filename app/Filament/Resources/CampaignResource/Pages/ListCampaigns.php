<?php

namespace App\Filament\Resources\CampaignResource\Pages;

use App\Filament\Resources\CampaignResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Campaign;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\LinkAction;
use Filament\Tables\Actions\ButtonAction;
use Filament\Tables\Actions\IconButtonAction;
use App\services\CampaignService;
use SergiX44\Nutgram\Nutgram;
use Html2Text\Html2Text as HTML2TEXT;

class ListCampaigns extends ListRecords
{
    protected static string $resource = CampaignResource::class;

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('gm_text')
                ->html()->searchable()
                ->label('content'),
            BadgeColumn::make('status')
                ->colors([
                    'danger' => fn ($state): bool => $state == false,
                    'success' => fn ($state): bool => $state == true,
                ])->enum([
                    false => "Drafted",
                    true => "Published"
                ])
        ];
    }

    protected function getTableActions(): array
    {
        return [
            IconButtonAction::make('Edit')
                ->url(fn (Campaign $record): string => "campaigns/$record->id/edit")->icon('heroicon-o-pencil')
                ->hidden(fn (Campaign $record): bool => $record->status),

            // IconButtonAction::make('Detail')
            //     ->url(fn (Campaign $record): string => "campaigns/$record->id/edit")->icon('heroicon-o-information-circle'),
            IconButtonAction::make('Delete')
                ->action(fn (Campaign $record) => $record->delete())
                ->requiresConfirmation()
                ->icon('heroicon-o-trash')->color('danger')
                ->hidden(fn (Campaign $record): bool => $record->status),
            ButtonAction::make('Publishe')
                ->action('publisheCampaign')
                ->requiresConfirmation()
                ->color('primary')
                ->hidden(fn (Campaign $record): bool => $record->status)

        ];
    }

    public function publisheCampaign(Nutgram $bot, Campaign $record)
    {
        // remove unsupported html tags
        $html = new HTML2TEXT($record->gm_text);
        // post 
        $message_ids = CampaignService::post($bot, $html->getText());
        // update the campaign status
        // dd($message_ids);
        $record->update(['message_ids' => $message_ids, 'status' => true]);
    }
}
