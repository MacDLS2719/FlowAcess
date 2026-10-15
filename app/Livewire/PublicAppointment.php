<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Appointment;
use App\Models\Availability;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PublicAppointment extends Component
{
    public $codigo;
    public $customer;
    public $mostrarAgenda = false;
    public $modoReagendar = false;
    public $appointmentActual;
    public $showModal = false;
    public $showAlreadyScheduledModal = false;
    public $confirmData = [];

    public function validarCodigo()
    {
        $this->customer = Customer::where('CodigoCustomer', $this->codigo)->first();

        if (! $this->customer) {
            $this->addError('codigo', 'Código no encontrado.');
            return;
        }

        $this->appointmentActual = Appointment::where('IdCustomer', $this->customer->IdCustomer)
            ->latest()
            ->first();

        // =========================
        // NO TIENE CITA
        // =========================
        if (! $this->appointmentActual) {
            $this->mostrarAgenda = true;
            return;
        }

        // =========================
        // PUEDE REAGENDAR
        // =========================
        if ($this->appointmentActual->Status === 'Reagendar') {
            $this->modoReagendar = true;
            $this->mostrarAgenda = true;
            return;
        }

        // =========================
        // BLOQUEO
        // =========================
        if ($this->appointmentActual->Status === 'Agendada') {
            $this->showAlreadyScheduledModal = true;
            return;
        }

        // Cancelada → permite nueva cita
        if ($this->appointmentActual->Status === 'Cancelada') {
            $this->mostrarAgenda = true;
            return;
        }
    }

    public function guardarCita(int $availabilityId)
    {
        $availability = Availability::find($availabilityId);

        if (! $availability || $availability->Status == 1) {
            $this->addError('horario', 'Horario no disponible.');
            return;
        }

        // =========================
        // REAGENDAR (EDITA EXISTENTE)
        // =========================
        if ($this->modoReagendar && $this->appointmentActual) {

            // liberar anterior horario
            Availability::where('IdAvailability', $this->appointmentActual->IdAvailability)
                ->update(['Status' => 0]);

            $this->appointmentActual->update([
                'IdAvailability' => $availability->IdAvailability,
                'Status' => 'Agendada',
                'Notes' => null,
            ]);

        } else {

            // =========================
            // NUEVA CITA
            // =========================
            Appointment::create([
                'IdCustomer' => $this->customer->IdCustomer,
                'IdAvailability' => $availability->IdAvailability,
                'Status' => 'Agendada',
                'Notes' => null,
            ]);
        }

        // bloquear nuevo horario
        $availability->update([
            'Status' => 1,
        ]);

        $this->confirmData = [
            'nombre' => $this->customer->Nombre,
            'codigo' => $this->customer->CodigoCustomer,
            'fecha' => \Carbon\Carbon::parse($availability->AvailableDate)->format('Y-m-d'),
            'hora' => \Carbon\Carbon::parse($availability->AvailableTime)->format('H:i')
        ];
        $this->showModal = true;

        $this->reset(['mostrarAgenda', 'modoReagendar']);
    }

    public function render()
    {
        $agendaOptions = collect();

        if ($this->mostrarAgenda && $this->customer) {
            $agendaOptions = Availability::where('Status', 0)
                ->orderBy('AvailableDate')
                ->orderBy('AvailableTime')
                ->get(['IdAvailability', 'AvailableDate', 'AvailableTime'])
                ->groupBy(fn (Availability $availability) => $availability->AvailableDate->format('Y-m-d'))
                ->map(function (Collection $slots, string $date) {
                    $parsedDate = Carbon::parse($date);

                    return [
                        'value' => $date,
                        'label' => $parsedDate->format('d M'),
                        'weekday' => ucfirst($parsedDate->translatedFormat('l')),
                        'slots' => $slots->map(fn (Availability $availability) => [
                            'id' => $availability->IdAvailability,
                            'label' => Carbon::parse($availability->AvailableTime)->format('H:i'),
                        ])->values(),
                    ];
                })
                ->values();
        }

        return view('livewire.public-appointment', [
            'agendaOptions' => $agendaOptions,
        ])
        ->layout('appointment');
    }
}