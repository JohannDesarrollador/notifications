<?php 

namespace JohannDesarrollador\Notifications\Http\Controllers;

use JohannDesarrollador\Notifications\Notificacion;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificacionController 
{

  public function crearNotificacion( $request )
  {

    $notificacion = Notificacion::create([
      'tipo' => $request->tipo,
      'titulo' => $request->titulo,
      'ruta' => $request->ruta,
      'mensaje' => $request->mensaje,
      'importancia' => $request->importancia,
      'destino' => $request->destino,
      'destino_id' => $request->destino_id,
    ]);

    if ($request->destino == 'rol')
    {
      // Asignar la notificación a todos los usuarios con el rol especificado
      $usuarios = User::where( 'rol' , $request->destino_id )->get();

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

    return response()->json(['message' => 'Notificación creada y asignada'], 201);

  }

  public function marcarComoLeida($usuario_id, $notificacion_id)
  {

    $usuario = User::findOrFail($usuario_id);

    $usuario->notificaciones()->updateExistingPivot($notificacion_id, [
      'leida' => true,
      'fecha_lectura' => Carbon::now(),
    ]);

    return response()->json(['message' => 'Notificación marcada como leída'], 200);

  }

  public function obtenerMisNotificaciones($usuario_id)
  {

    $usuario = User::findOrFail($usuario_id);
    $notificaciones = $usuario->notificaciones()->orderBy('created_at', 'desc')->get();

    return response()->json($notificaciones, 200);

  }

}
