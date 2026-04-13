<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Role extends Model
{
    protected $table = 'roles'; // Forzamos el nombre de la tabla
    protected $fillable = ['id_local', 'synced', 'nombre_rol'];

    // Boot method para generar el UUID (id_local) si se crea desde el servidor
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id_local)) {
                $model->id_local = (string) Str::uuid();
            }
        });
    }

    public function users()
    {
        return $this->hasMany(User::class, 'id_rol');
    }
}