<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('notificaciones', function (Blueprint $table) {
      $table->id();
      
      $table->string('tipo', 50);
      $table->string('titulo', 255);
      $table->text('mensaje');

      $table->text( 'ruta' );

      $table->timestamp('fecha_creacion')->useCurrent();
      $table->enum('importancia', ['baja', 'media', 'alta'])->default('media');
      $table->enum('destino', [ 'usuario' , 'rol' ]);
            
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('notificaciones');
  }

};
