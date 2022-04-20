<?php

namespace App\Filament\Resources\CampaignResource\Pages;

use App\Filament\Resources\CampaignResource;
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
use Filament\Resources\Pages\EditRecord;
use Closure;
use Illuminate\Support\Str;

class EditCampaign extends EditRecord
{
    protected static string $resource = CampaignResource::class;
    
    protected function getFormSchema(): array
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

        return [

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
            Section::make('Bot Message')
                ->columns(1)
                ->schema([
                    Forms\Components\FileUpload::make('bm_image')->image()->label('Image')->required(),
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
                        ->afterStateUpdated(function (Closure $set, $state) use ($tz) {
                            $set(
                                'duration',
                                Carbon::parse($state, $tz)->diffForHumans(now()->tz($tz))
                            );
                        })
                        ->required()
                        ->minDate(now()->subDay())
                        ->helperText(fn ($state, callable $set) => $set('duration', $state ? Carbon::parse($state, $tz)->diffForHumans(Carbon::parse()) : ''))
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



        ];
    }
}
