<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessHistory extends Model
{
    protected $table = 'process_histories';

    protected $primaryKey = 'idHistory';

    protected $fillable = [
        'idProcess',
        'idUser',
        'Estado',
        'Observacion',
    ];

    public function process()
    {
        return $this->belongsTo(Process::class, 'idProcess', 'IdProcess');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'idUser', 'id');
    }
}