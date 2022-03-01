<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;
    protected $fillable = [
        "id_agenda_superior",
        "nome",
        "inicio",
        "fim"
    ];

    public function filhas()
    {
        return $this->hasMany(Agenda::class, 'id_agenda_superior', 'id');
    }

    public function mae()
    {
        return $this->belongsTo(Agenda::class, 'id');
    }
}