<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Campaign;
use App\Models\User;
use App\Models\Order;

class StatsOverview extends BaseWidget
{

    protected function getCards(): array
    {
        return [
            Card::make('Campaigns', Campaign::count()),
            Card::make('Clients', User::count()),
            Card::make('Orders', Order::count()),
            Card::make('New Orders', Order::where('status', 1)->count()),
        ];
    }
}
