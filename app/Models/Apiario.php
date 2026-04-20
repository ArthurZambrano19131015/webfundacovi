<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Apiario extends Model
{
    protected $fillable = [
        'id_local', 'id_apicultor', 'nombre', 'latitud', 'longitud', 'municipio', 'estado_activo', 'synced'
    ];

    // Regla de Oro: Generar el UUID local automáticamente al crear en servidor
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id_local)) {
                $model->id_local = (string) Str::uuid();
            }
        });
    }

    public function apicultor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_apicultor');
    }

    public function colmenas(): HasMany
    {
        return $this->hasMany(Colmena::class, 'id_apiario');
    }
}