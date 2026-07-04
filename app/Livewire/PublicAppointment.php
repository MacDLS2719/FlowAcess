<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Appointment;
use App\Models\Availability;

class PublicAppointment extends Component
{
    public $codigo;
    public $customer;
    public $fechaSeleccionada;
    public $horarioSeleccionado;
    public $mostrarAgenda = false;
    public $modoReagendar = false;
    public $appointmentActual;
    public $showModal = false;
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
            $this->addError('codigo', 'Ya tienes una cita activa.');
            return;
        }

        // Cancelada → permite nueva cita
        if ($this->appointmentActual->Status === 'Cancelada') {
            $this->mostrarAgenda = true;
            return;
        }
    }

    public function guardarCita()
    {
        $availability = Availability::find($this->horarioSeleccionado);

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
        $fechas = Availability::where('Status', 0)
            ->select('AvailableDate')
            ->distinct()
            ->orderBy('AvailableDate')
            ->get();

        $horarios = [];

        if ($this->fechaSeleccionada) {

            $horarios = Availability::where(
                'AvailableDate',
                $this->fechaSeleccionada
            )
            ->where('Status', 0)
            ->orderBy('AvailableTime')
            ->get();
        }

        return view('livewire.public-appointment', [
            'fechas' => $fechas,
            'horarios' => $horarios,
        ])
        ->layout('appointment');
    }
}