<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Availability;
use App\Models\Appointment;
use App\Models\Customer;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class AgendaCalendario extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Calendario de Agenda';
    protected static ?string $title = 'Calendario de Disponibilidad';
    protected static ?string $navigationGroup = 'Operaciones';
    protected static string $view = 'filament.pages.agenda-calendario';

    // =========================
    // CALENDARIO
    // =========================
    public $currentMonth;
    public $currentYear;

    // =========================
    // MODAL
    // =========================
    public $showModal = false;
    public ?Customer $customer = null;
    public ?Appointment $appointment = null;
    public ?Collection $histories = null;
    public $hasAppointment = false;

    public $editMode = false;

    public $EstadoGeneral;
    public $Observacion;

    public $appointmentStatus;
    public $appointmentNotes;
    public $appointmentDate;
    public $appointmentTime;

    // =========================
    // INIT
    // =========================
    public function mount(): void
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
    }

    // =========================
    // CALENDARIO NAV
    // =========================
    public function prevMonth(): void
    {
        $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
    }

    public function nextMonth(): void
    {
        $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
    }

    public function goToToday(): void
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
    }

    // =========================
    // VIEW DATA (CALENDARIO)
    // =========================
    protected function getViewData(): array
    {
        $startOfMonth = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $availabilities = Availability::with('appointments.customer')
            ->whereBetween('AvailableDate', [
                $startOfMonth->format('Y-m-d'),
                $endOfMonth->format('Y-m-d')
            ])
            ->orderBy('AvailableTime')
            ->get();

        $daysInMonth = $startOfMonth->daysInMonth;
        $firstDayOfWeek = $startOfMonth->dayOfWeek;

        $availabilitiesByDay = [];

        foreach ($availabilities as $availability) {
            $day = Carbon::parse($availability->AvailableDate)->day;

            if (!isset($availabilitiesByDay[$day])) {
                $availabilitiesByDay[$day] = [];
            }

            $availabilitiesByDay[$day][] = $availability;
        }

        return [
            'monthName' => ucfirst($startOfMonth->translatedFormat('F Y')),
            'daysInMonth' => $daysInMonth,
            'firstDayOfWeek' => $firstDayOfWeek,
            'availabilitiesByDay' => $availabilitiesByDay,
        ];
    }

    // =========================
    // OPEN MODAL (NUEVO FLUJO)
    // =========================
    public function openModal(int $customerId, bool $edit = false): void
    {
        $this->customer = Customer::with([
            'processes.histories.user',
            'appointments.availability',
        ])->findOrFail($customerId);

        $this->editMode = $edit;

        $this->EstadoGeneral = $this->customer->EstadoGeneral;
        $this->Observacion = '';

        // Historial
        $process = $this->customer->processes->first();

        $this->histories = $process
            ? $process->histories()->with('user')->latest()->get()
            : new Collection();

        // Appointment
        $this->appointment = $this->customer->appointments->first();

        $this->hasAppointment = $this->appointment !== null;

        if ($this->appointment) {
            $this->appointmentStatus = $this->appointment->EstadoAgen ?? null;
            $this->appointmentNotes = $this->appointment->Notes ?? null;
            $this->appointmentDate = $this->appointment->availability?->AvailableDate instanceof \Carbon\Carbon 
                ? $this->appointment->availability->AvailableDate->format('Y-m-d') 
                : $this->appointment->availability?->AvailableDate;
            $this->appointmentTime = $this->appointment->availability?->AvailableTime;
        }

        $this->showModal = true;
    }

    // =========================
    // CLOSE MODAL
    // =========================
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
            'appointmentStatus',
            'appointmentNotes',
            'appointmentDate',
            'appointmentTime',
            'editMode',
        ]);
    }

    // =========================
    // SAVE CHANGES
    // =========================
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

            $status = $this->appointment->Status;

            if ($this->appointmentStatus === 'No Asistio') {
                $status = 'Reagendar';
            }

            $this->appointment->update([
                'EstadoAgen' => $this->appointmentStatus,
                'Status' => $status,
                'Notes' => $this->appointmentNotes,
            ]);
        }

        Notification::make()
            ->title('Cambios guardados correctamente')
            ->success()
            ->send();

        $this->closeModal();
    }
}