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
                ->getStateUsing(fn ($record) => Carbon::parse($record->bm_apply_btn_active_duration, env('ADMIN_TIMEZONE'))->diffForHumans(Carbon::parse($record->updated_at), env('ADMIN_TIMEZONE'))),

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
            IconButtonAction::make('Delete')
                ->action('deleteCampaign')
                ->requiresConfirmation()
                ->icon('heroicon-o-trash')->color('danger'),
            ButtonAction::make('Publishe')
                ->action('publisheCampaign')
                ->requiresConfirmation()
                ->color('primary'),
            // IconButtonAction::make('view')
            //     ->url(fn (Campaign $record): string => "campaigns/$record->id")
            //     ->icon('heroicon-o-eye')

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


    public function deleteCampaign(Campaign $record)
    {
        // check for the presence of previous post if found delete them first
        dd("edit delete");
        if ($record->message_ids) {

            CampaignService::deleteGroupMessage($record->message_ids);
        }
        $record->delete();
    }
}
