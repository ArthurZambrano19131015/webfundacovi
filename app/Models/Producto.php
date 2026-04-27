<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Producto extends Model
{
    protected $fillable = ['id_local', 'id_apiario', 'nombre', 'precio', 'foto', 'observaciones', 'estado_activo', 'synced'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id_local)) $model->id_local = (string) Str::uuid();
        });
    }

    public function apiario()
    {
        return $this->belongsTo(Apiario::class, 'id_apiario');
    }
}
