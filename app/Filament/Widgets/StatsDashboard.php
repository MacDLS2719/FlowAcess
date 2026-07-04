<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Customer;

class StatsDashboard extends BaseWidget
{
    protected function getCards(): array
    {
        return [

            Card::make(
                'Pendientes',
                Customer::where('EstadoGeneral', 'Pendiente')->count()
            )
                ->description('Clientes registrados')
                ->color('gray')
                ->icon('heroicon-o-clock'),

            Card::make(
                'En Proceso',
                Customer::where('EstadoGeneral', 'En Proceso')->count()
            )
                ->description('Procesos activos')
                ->color('warning')
                ->icon('heroicon-o-cog-6-tooth'),

            Card::make(
                'Decorados',
                Customer::where('EstadoGeneral', 'Decorado')->count()
            )
                ->description('Pendientes de entrega')
                ->color('info')
                ->icon('heroicon-o-sparkles'),

            Card::make(
                'Entregados',
                Customer::where('EstadoGeneral', 'Entregado')->count()
            )
                ->description('Procesos finalizados')
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Card::make(
                'Pagados',
                Customer::where('EstadoContable', 'Pago')->count()
            )
                ->description('Clientes al día')
                ->color('success')
                ->icon('heroicon-o-banknotes'),

            Card::make(
                'Pendientes de Pago',
                Customer::where('EstadoContable', 'Debe')->count()
            )
                ->description('Clientes con saldo pendiente')
                ->color('danger')
                ->icon('heroicon-o-exclamation-circle'),

        ];
    }
}