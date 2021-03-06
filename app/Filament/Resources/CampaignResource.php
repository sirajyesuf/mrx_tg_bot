<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampaignResource\Pages;
use App\Filament\Resources\CampaignResource\RelationManagers;
use App\Models\Campaign;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\LinkAction;
use Filament\Widgets\Widget;
use Filament\Forms\Components\Fieldset;
use App\Filament\Resources\CampaignResource\Widgets\StatsOverview;
use App\Models\Country;
use App\Models\Interest;
use App\Models\Payment;
use Carbon\Carbon;
use Filament\Forms\Components\MarkdownEditor;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;
    protected static ?string $navigationIcon = 'heroicon-o-collection';


    public static function form(Form $form): Form
    {
        $tz = auth()->user()->time_zone;

        $interestes = array();
        foreach (Interest::all()->pluck('name') as $int) {
            $interestes[$int] = $int;
        }
        $geo = array();
        foreach (Country::all()->pluck('name') as $ctry) {

            $geo[$ctry] = $ctry;
        }
        $payment = array();
        foreach (Payment::all()->pluck('name') as $pay) {

            $payment[$pay] = $pay;
        }

        return $form
            ->schema([

                Section::make('Group Message')
                    ->columns(1)
                    ->schema([

                        Forms\Components\TextInput::make('title')
                            ->unique(ignorable: fn (?Campaign $record): ?Campaign => $record)
                            ->required()
                            ->columns(),
                        TinyEditor::make('gm_text')
                            ->label('Text')
                            ->required()
                            ->profile('mrx')


                    ]),
                // Fieldset::make('Group Message')
                //     ->schema([
                //         // Forms\Components\RichEditor::make('gm_text')->toolbarButtons(
                //         //     [

                //         //         'bold',
                //         //         'italic',
                //         //         'link',

                //         //     ]
                //         // )->label('Content')->required(),
                //         // TinyEditor::make('gm_text')->profile('mrx'),




                //     ])->columns(1),

                Section::make('Bot Message')
                    ->columns(1)
                    ->schema([
                        Forms\Components\FileUpload::make('bm_image')->image()->label('Image')->required(),
                        // Forms\Components\RichEditor::make('bm_text')->toolbarButtons([
                        //     'bold',
                        //     'italic',
                        //     'link',
                        // ])->label('content')->required(),
                        TinyEditor::make('bm_text')
                            ->label('Text')
                            ->required()
                            ->profile('mrx')




                    ]),
                Section::make('Settings')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('gm_claim_now_btn_num_click')
                            ->numeric()->minValue(1)->label('claim now btn number')->required(),
                        Forms\Components\DateTimePicker::make('bm_apply_btn_active_duration')
                            ->label('Apply btn duration')
                            ->required()
                            ->minDate(now()->subDay())
                            ->placeholder(now()->tz($tz))
                            ->helperText(fn ($state, callable $set) => $set('duration', $state ? Carbon::parse($state, $tz)->diffForHumans(now()->tz($tz)) : ''))
                            ->reactive(),
                        Forms\Components\TextInput::make('bm_apply_btn_url')->url()->label('Apply btn url')->required(),
                        Forms\Components\MultiSelect::make('payment_methods')
                            ->label('Payment')
                            ->options($payment)
                            ->required(),

                    ]),
                Section::make('Apply Filters')
                    ->columns(2)
                    ->schema([
                        Forms\Components\MultiSelect::make('gm_geo')
                            ->label('Geo')
                            ->options($geo),
                        Forms\Components\MultiSelect::make('gm_interest')
                            ->label('Interest and Prime')
                            ->options($interestes),





                    ]),



            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ]);
    }
    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     $data['bm_apply_btn_active_duration'] = Carbon::parse($data['bm_apply_btn_active_duration'], 'UTC');

    //     return $data;
    // }
    // protected function mutateFormDataBeforeSave(array $data): array
    // {

    //     $data['bm_apply_btn_active_duration'] = Carbon::parse($data['bm_apply_btn_active_duration'], 'UTC');

    //     return $data;
    // }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCampaigns::route('/'),
            'create' => Pages\CreateCampaign::route('/create'),
            'edit' => Pages\EditCampaign::route('/{record}/edit'),
            'view' => Pages\ViewCampaign::route('/{record}'),
            'stats' => Pages\StatsCampaign::route('{record}/stats')
        ];
    }
}
