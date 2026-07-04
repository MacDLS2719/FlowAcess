<?php

namespace App\Filament\General\Pages;

use Filament\Pages\Page;
use App\Filament\General\Widgets\GeneralStats;

class GeneralDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Escritorio';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static bool $shouldRegisterNavigation = false;

     protected function getHeaderWidgets(): array
    {
        return [
            GeneralStats::class,
        ];
    }

}
