<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LoteCalidad extends Model
{
    protected $fillable =[
        'id_local', 'id_registrado_por', 'codigo_lote', 'estado_aprobacion', 'synced'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id_local)) $model->id_local = (string) Str::uuid();
        });
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'id_registrado_por');
    }

    public function cosechas()
    {
        return $this->hasMany(Cosecha::class, 'id_lote');
    }

    public function resultados()
    {
        return $this->hasMany(ResultadoParametro::class, 'id_lote');
    }
}
