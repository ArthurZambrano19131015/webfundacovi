<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GuiaInformativa extends Model
{
    protected $fillable = ['id_local', 'id_autor', 'titulo', 'contenido', 'synced'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id_local)) $model->id_local = (string) Str::uuid();
        });
    }

    public function autor()
    {
        return $this->belongsTo(User::class, 'id_autor');
    }
}
