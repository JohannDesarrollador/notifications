<?php 

namespace JohannDesarrollador\Notifications\Utils;

use JohannDesarrollador\Notifications\Models\Notificacion;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificacionUtil
{

  public function crearNotificacion( $request )
  {

    $notificacion = Notificacion::create([
      'tipo' => $request->tipo,
      'titulo' => $request->titulo,
      'mensaje' => $request->mensaje,
      'importancia' => $request->importancia,
      'destino' => $request->destino,
      'destino_id' => $request->destino_id,
    ]);

    if ($request->destino == 'rol')
    {
      // Asignar la notificación a todos los usuarios con el rol especificado
      $usuarios = User::where( 'current_role_id' , $request->destino_id )->get();

      foreach ($usuarios as $usuario)
      {
        $usuario->notificaciones()->attach( $notificacion->id );
      }

    }
    elseif ( $request->destino == 'usuario' )
    {
      // Asignar la notificación al usuario específico
      $usuario = User::findOrFail($request->destino_id);
      $usuario->notificaciones()->attach( $notificacion->id );
    }

    return ['message' => 'Notificación creada y asignada'];

  }

  public function newUser( $role , $id )
  {

    $ntf = (object)[
      "tipo"        => "users",
      "titulo"      => "Nuevo Usuario",
      "mensaje"     => "Se ha creado un nuevo usuario (".$role->name.")",
      "importancia" => "media",
      "destino"     => "rol",
      "destino_id"  => $role->id,
      'ruta'        => '/user/'.$id,
    ];

    $result = $this->crearNotificacion( $ntf );

    return $result;
    
  }

}
