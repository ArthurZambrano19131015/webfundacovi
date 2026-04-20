<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Colmena extends Model
{
    protected $fillable = [
        'id_local', 'id_apiario', 'identificador', 'tipo_colmena', 'fecha_instalacion', 'estado_activo', 'synced'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id_local)) {
                $model->id_local = (string) Str::uuid();
            }
        });
    }

    public function apiario(): BelongsTo
    {
        return $this->belongsTo(Apiario::class, 'id_apiario');
    }
}