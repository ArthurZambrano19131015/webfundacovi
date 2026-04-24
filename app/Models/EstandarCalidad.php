<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EstandarCalidad extends Model
{
    protected $fillable =[
        'id_local', 'parametro', 'valor_minimo', 'valor_maximo', 'unidad_medida', 'estado_activo', 'synced'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id_local)) $model->id_local = (string) Str::uuid();
        });
    }
}