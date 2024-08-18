<?php

namespace JohannDesarrollador\Notifications\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationUser extends Model
{

  protected $table = 'notificaciones_usuarios';

  protected $fillable = [
    'usuario_id', 
    'notificacion_id', 
    'leida', 
    'fecha_lectura'
  ];

  public function usuarios()
  {
    return $this->belongsToMany(User::class, 'notificaciones_usuarios', 'notificacion_id', 'usuario_id')
    ->withPivot('leida', 'fecha_lectura')
    ->withTimestamps();
  }

}
