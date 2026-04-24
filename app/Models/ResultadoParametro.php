<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ResultadoParametro extends Model
{
    protected $fillable =[
        'id_local', 'id_lote', 'id_estandar', 'valor_obtenido', 'cumple_estandar', 'synced'
    ];

    protected $casts =[
        'cumple_estandar' => 'boolean',
        'synced' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id_local)) $model->id_local = (string) Str::uuid();
        });
    }

    public function lote()
    {
        return $this->belongsTo(LoteCalidad::class, 'id_lote');
    }

    public function estandar()
    {
        return $this->belongsTo(EstandarCalidad::class, 'id_estandar');
    }
}