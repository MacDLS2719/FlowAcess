<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\Process;
use App\Models\ProcessHistory;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [

            Actions\DeleteAction::make()
                ->visible(fn () => Auth::user()->hasRole('Admin')),

        ];
    }

    protected function afterSave(): void
    {
        $data = $this->data;
        $process = Process::firstOrCreate(
            [
                'IdCustomer' => $this->record->IdCustomer,
            ],
            [
                'EstadoActual' => $data['EstadoGeneral'],
                'FechaInicio'  => now()->toDateString(),
                'FechaCierre'  => null,
            ]
        );

        $process->update([
            'EstadoActual' => $data['EstadoGeneral'],
            'FechaCierre' => $data['EstadoGeneral'] === 'Entregado'
                ? now()->toDateString()
                : null,
        ]);


        if (! empty($data['Observacion'])) {

            ProcessHistory::create([
                'idProcess'   => $process->IdProcess,
                'idUser'      => Auth::id(),
                'Estado'      => $data['EstadoGeneral'],
                'Observacion' => $data['Observacion'],
            ]);

        }
    }

    protected function getRedirectUrl(): string
    {
        return CustomerResource::getUrl('index');
    }
}