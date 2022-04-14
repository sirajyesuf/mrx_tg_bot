<?php

namespace App\Filament\Resources\CampaignResource\Pages;

use App\Filament\Resources\CampaignResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Campaign;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\ButtonAction;
use Filament\Tables\Actions\IconButtonAction;
use App\services\CampaignService;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use DateInterval;
use SergiX44\Nutgram\Nutgram;
use Filament\Tables\Columns\ImageColumn;

class ListCampaigns extends ListRecords
{
    protected static string $resource = CampaignResource::class;

    protected function getTableColumns(): array
    {


        return [

            TextColumn::make('title')->label('Campaign'),
            TagsColumn::make('gm_interest')->label('Interestes_Prime')->default('not applyed'),
            TagsColumn::make('gm_geo')->label('Geo')->default('not applyed'),
            TagsColumn::make('payment_methods')->label('payment_methods')->default('not applyed'),
            TextColumn::make('gm_claim_now_btn_num_click')->label('Num_Claim'),
            TextColumn::make('bm_apply_btn_active_duration')->label('Apply_Btn_Duration')
                ->getStateUsing(fn ($record) => Carbon::parse($record->bm_apply_btn_active_duration)->diffForHumans($record->updated_at)),
            // ->formatStateUsing(fn (DateInterval $state): string => $state->d . ' days, ' . $state->h . ' hours, ' . $state->i . ' minutes'),
            // BadgeColumn::make('status')
            //     ->colors([
            //         'danger' => fn ($state): bool => $state == false,
            //         'success' => fn ($state): bool => $state == true,
            //     ])->enum([
            //         false => "Drafted",
            //         true => "Published"
            //     ])
        ];
    }

    protected function getTableActions(): array
    {
        return [
            // IconButtonAction::make('Edit')
            //     ->url(fn (Campaign $record): string => "campaigns/$record->id/edit")->icon('heroicon-o-pencil')
            //     ->hidden(fn (Campaign $record): bool => $record->status),

            // IconButtonAction::make('Detail')
            //     ->url(fn (Campaign $record): string => "campaigns/$record->id/edit")->icon('heroicon-o-information-circle'),
            // IconButtonAction::make('Delete')
            //     ->action(fn (Campaign $record) => $record->delete())
            //     ->requiresConfirmation()
            //     ->icon('heroicon-o-trash')->color('danger')
            // ->hidden(fn (Campaign $record): bool => $record->status),
            ButtonAction::make('Publishe')
                ->action('publisheCampaign')
                ->requiresConfirmation()
                ->color('primary'),
            IconButtonAction::make('view')
                ->url(fn (Campaign $record): string => "campaigns/$record->id")
                ->icon('heroicon-o-eye')

        ];
    }

    public function publisheCampaign(Nutgram $bot, Campaign $record)
    {
        // check for the presence of previous post if found delete them first

        if ($record->message_ids) {

            CampaignService::deleteGroupMessage($record->message_ids);
        }

        // post 
        $message_ids = CampaignService::post($bot, $record);
        // update the campaign status
        $record->update(['message_ids' => $message_ids]);
        //send success notification
        $this->notify('success', 'Published');
    }
}
