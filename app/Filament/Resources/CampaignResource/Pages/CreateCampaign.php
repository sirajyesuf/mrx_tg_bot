<?php

namespace App\Filament\Resources\CampaignResource\Pages;

use App\Filament\Resources\CampaignResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Forms\Components\Fieldset;
use App\Models\Country;
use App\Models\Interest;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class CreateCampaign extends CreateRecord
{
    protected static string $resource = CampaignResource::class;

    public  function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Group Message')
                    ->schema([
                        Forms\Components\RichEditor::make('gm_text')->toolbarButtons(
                            [

                                'bold',
                                'italic',
                                'link',

                            ]
                        )->label('Content')->required(),
                        // TinyEditor::make('content')->profile('mrx')




                    ])->columns(1),

                Fieldset::make('Bot Message')
                    ->schema([
                        Forms\Components\FileUpload::make('bm_image')->image(),
                        Forms\Components\RichEditor::make('bm_text')->toolbarButtons([
                            'bold',
                            'italic',
                            'link',
                        ])->label('content')->required()
                        // TinyEditor::make('bm_text')




                    ])->columns(1),
                Fieldset::make('Settings')
                    ->schema([
                        Forms\Components\TextInput::make('gm_claim_now_btn_num_click')
                            ->numeric()->minValue(1)->label('claim now btn number')->required(),
                        Forms\Components\DateTimePicker::make('bm_apply_btn_active_duration')->label('apply btn duration')->required(),
                        Forms\Components\TextInput::make('bm_apply_btn_url')->url()->label('apply btn url')->required()







                    ])->columns(1),
                Fieldset::make('Apply Filters')
                    ->schema([
                        Forms\Components\MultiSelect::make('gm_geo')
                            ->label('Geo')
                            ->options(Country::all()->pluck('name')),
                        Forms\Components\MultiSelect::make('gm_interest')
                            ->label('interest')
                            ->options(Interest::all()->pluck('name')),





                    ])->columns(1)


            ]);
    }
}
