<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Cosecha extends Model
{
    protected $fillable =[
        'id_local', 'id_colmena', 'id_lote', 'fecha_recoleccion', 'cantidad_kg', 'novedades', 'synced'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id_local)) $model->id_local = (string) Str::uuid();
        });
    }

    public function colmena()
    {
        return $this->belongsTo(Colmena::class, 'id_colmena');
    }

    public function lote()
    {
        return $this->belongsTo(LoteCalidad::class, 'id_lote');
    }
}