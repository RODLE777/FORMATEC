<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    public $timestamps = false;
    protected $fillable = ['municipio_id', 'nombre'];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }
}
