<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Paket_data extends Model
{
    protected $table = 'paket';
    protected $fillable = [
        'kdsatker', 'nmpaket', 'pagurmp', 'trgoutput', 'satoutput', 'trgoutcome', 'satoutcome', 'kdoutput', 'TahunFisik', 'pagurmawal', 'keuangan', 'progres_fisik'
    ];
}
