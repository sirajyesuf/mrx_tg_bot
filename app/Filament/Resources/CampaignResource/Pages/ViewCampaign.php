<?php

namespace App\Filament\Resources\CampaignResource\Pages;

use App\Filament\Resources\CampaignResource;
use App\Models\Campaign;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\FileUpload;
use Filament\Pages\Actions\ButtonAction;
use Filament\Forms\Components\Card;

class ViewCampaign extends Page
{
    protected static string $resource = CampaignResource::class;

    protected static string $view = 'filament.resources.campaign-resource.pages.view-campaign';
    public $record;
    public function mount($record)
    {

        $this->record = Campaign::find($record);
    }

    protected function getFormSchema(): array
    {
        $path  = asset("storage/" . $this->record->bm_image);
        $html = new HtmlString('<img src=' . $path . '>');
        return [

            Card::make()
                ->schema([
                    Placeholder::make('Group Message')
                        ->content(new HtmlString($this->record->gm_text)),
                    Placeholder::make("Bot Message")
                        ->content(
                            $html
                        ),
                    Placeholder::make('')
                        ->content(new HtmlString($this->record->bm_text)),
                ])
                ->columns(1)






        ];
    }

    protected function getActions(): array
    {
        return [
            ButtonAction::make('Edit')
                ->url(fn (): string => $this->getResource()::getUrl('edit', $this->record->id))
        ];
    }
}
