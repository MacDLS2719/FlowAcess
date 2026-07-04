<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    protected $table = 'process';

    protected $primaryKey = 'IdProcess';

    protected $fillable = [
        'IdCustomer',
        'EstadoActual',
        'FechaInicio',
        'FechaCierre',
    ];

    protected $casts = [
        'FechaInicio' => 'date',
        'FechaCierre' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'IdCustomer', 'IdCustomer');
    }

    public function histories()
    {
        return $this->hasMany(ProcessHistory::class, 'IdProcess', 'IdProcess');
    }
}