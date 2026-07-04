<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customer';

    protected $primaryKey = 'IdCustomer';

    protected $fillable = [
        'Nombre',
        'Telefono',
        'Sexo',
        'Tipo',
        'EstadoContable',
        'EstadoGeneral',
        'CodigoCustomer',
    ];

    public function processes()
    {
        return $this->hasMany(Process::class, 'IdCustomer', 'IdCustomer');
    }

    public function appointments()
    {
        return $this->hasMany(
            Appointment::class,
            'IdCustomer',
            'IdCustomer'
        );
    }
}
