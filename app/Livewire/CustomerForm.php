<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;

class CustomerForm extends Component
{
    public $Nombre;
    public $Telefono;
    public $Sexo;
    public $Tipo;
    public $EstadoContable;

    public $codigoGenerado;
    public $showModal = false;
    public $customerRegistrado;

    protected $rules = [
        'Nombre' => 'required|string|max:255',
        'Telefono' => 'required|string|max:11',
        'Sexo' => 'required|string',
        'Tipo' => 'required|in:Adulto,Niño,Carnaval,Personalizado',
        'EstadoContable' => 'required|in:Pago,Debe',
    ];

    public function save()
    {
        $this->validate();

        $customer = Customer::create([
            'Nombre' => $this->Nombre,
            'Telefono' => $this->Telefono,
            'Sexo' => $this->Sexo,
            'Tipo' => $this->Tipo,
            'EstadoContable' => $this->EstadoContable,
            'EstadoGeneral' => 'Pendiente',
            'CodigoCustomer' => '',
        ]);

        $codigo = match ($this->Tipo) {
            'Adulto' => 'ADUL-' . $customer->IdCustomer,
            'Niño' => 'NIN-' . $customer->IdCustomer,
            'Carnaval' => 'CAR-' . $customer->IdCustomer,
            'Personalizado' => 'PER-' . $customer->IdCustomer,
        };

        $customer->update([
            'CodigoCustomer' => $codigo,
        ]);

        $this->customerRegistrado = $customer->fresh();
        $this->codigoGenerado = $codigo;
        $this->showModal = true;

        $this->reset([
            'Nombre',
            'Telefono',
            'Sexo',
            'Tipo',
            'EstadoContable',
        ]);
    }

    public function render()
    {
        return view('livewire.customer-form');
    }
}