<?php

namespace App\Filament\Pages;

use App\Models\Customer;
use Filament\Pages\Page;

class CustomerTracking extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?string $navigationLabel = 'Seguimiento Clientes';
    protected static ?string $navigationGroup = 'Operaciones';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.customer-tracking';


    public ?string $codigo = '';
    public ?string $nombre = '';
    public ?string $estado = '';

    public $customers = [];

  
    public bool $showModal = false;
    public bool $editMode = false;
    public ?Customer $customer = null;



    public ?string $EstadoGeneral = null;
    public ?string $Observacion = null;

    public bool $hasAppointment = false;
    public $appointment = null;
    public $histories = [];

    public ?string $appointmentStatus = null;
    public ?string $appointmentNotes = null;

    public function mount(): void
    {
        $this->buscar();
    }

    public function updatedCodigo(): void
    {
        $this->buscar();
    }

    public function updatedNombre(): void
    {
        $this->buscar();
    }

    public function updatedEstado(): void
    {
        $this->buscar();
    }

    public function buscar(): void
    {
        $this->customers = Customer::query()

            ->when(
                filled($this->codigo),
                fn ($query) =>
                    $query->where(
                        'CodigoCustomer',
                        'like',
                        "%{$this->codigo}%"
                    )
            )

            ->when(
                filled($this->nombre),
                fn ($query) =>
                    $query->where(
                        'Nombre',
                        'like',
                        "%{$this->nombre}%"
                    )
            )

            ->when(
                filled($this->estado),
                fn ($query) =>
                    $query->where(
                        'EstadoGeneral',
                        $this->estado
                    )
            )

            ->latest()

            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Abrir Modal
    |--------------------------------------------------------------------------
    */

    public function openModal(int $id, bool $edit = false): void
    {
        $this->customer = Customer::with([
            'processes.histories.user',
            'appointments',
        ])->findOrFail($id);

        $this->editMode = $edit;

        $this->EstadoGeneral = $this->customer->EstadoGeneral;

        $this->Observacion = '';

        /*
        |--------------------------------------------------------------------------
        | Historial
        |--------------------------------------------------------------------------
        */

        $process = $this->customer->processes->first();

        $this->histories = $process
            ? $process->histories()->with('user')->latest()->get()
            : collect();

        /*
        |--------------------------------------------------------------------------
        | Agendamiento
        |--------------------------------------------------------------------------
        */

        $this->appointment = $this->customer
            ->appointments()
            ->with('availability')
            ->first();

        $this->hasAppointment = $this->appointment != null;

        $this->showModal = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Cerrar Modal
    |--------------------------------------------------------------------------
    */

    public function closeModal(): void
    {
        $this->reset([
            'showModal',
            'customer',
            'appointment',
            'histories',
            'EstadoGeneral',
            'Observacion',
            'hasAppointment',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Guardar
    |--------------------------------------------------------------------------
    */

    public function save(): void
    {
        $userId = auth()->id();

        $this->customer->update([
            'EstadoGeneral' => $this->EstadoGeneral,
        ]);

        $process = $this->customer->processes()->first();

        if (! $process) {
            $process = $this->customer->processes()->create([
                'EstadoActual' => $this->EstadoGeneral,
                'FechaInicio' => now(),
            ]);
        } else {
            $process->update([
                'EstadoActual' => $this->EstadoGeneral,
            ]);
        }

        if ($this->EstadoGeneral === 'Entregado') {
            $process->update([
                'FechaCierre' => now(),
            ]);
        }

        $process->histories()->create([
            'idUser' => $userId,
            'Estado' => $this->EstadoGeneral,
            'Observacion' => $this->Observacion,
        ]);


        if ($this->appointment) {

            $estadoAgen = $this->appointmentStatus;

            $status = $this->appointment->Status;

            if ($this->appointmentStatus === 'No Asistio') {
                $status = 'Reagendar';
            }

            $this->appointment->update([
                'EstadoAgen' => $estadoAgen,
                'Status' => $status,
                'Notes' => $this->appointmentNotes,
            ]);
        }
        $this->closeModal();
        $this->buscar();
    }
}