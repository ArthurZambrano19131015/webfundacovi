<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable =[
        'id_local',
        'synced',
        'id_rol',
        'nombre_completo',
        'email',
        'telefono',
        'foto',
        'password',
        'estado_activo'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return[
            'password' => 'hashed',
            'estado_activo' => 'boolean',
            'synced' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id_local)) {
                $model->id_local = (string) Str::uuid();
            }
        });
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_rol');
    }
}
