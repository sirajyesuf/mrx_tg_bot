<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Campaign;
use App\Models\User;
class StatsOverview extends BaseWidget
{

    protected function getCards(): array
    {
        return [
            Card::make('Campaigns',Campaign::count()),
            Card::make('Clients', User::count())

            
        ];
    }
}
