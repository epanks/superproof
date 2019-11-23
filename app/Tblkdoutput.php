<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tblkdoutput extends Model
{
    protected $table = 'tblkdoutput';

    public function tblabat()
    {
        return $this->belongsTo(Tblabat::class);
    }

    public function paket_data()
    {
        return $this->hasMany(Paket_data::class);
    }
}
