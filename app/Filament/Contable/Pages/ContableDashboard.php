<?php

namespace App\Filament\Contable\Pages;

use Filament\Pages\Page;
use App\Filament\Contable\Widgets\ContableStats;

class ContableDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Escritorio';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static bool $shouldRegisterNavigation = false;

    protected function getHeaderWidgets(): array
    {
        return [
            ContableStats::class,
        ];
    }
}
