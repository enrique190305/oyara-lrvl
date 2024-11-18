<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Network\LoginController;

Route::get('/', [LoginController::class, 'acceso'])->name('acceso');
Route::post('/acceso', [LoginController::class, 'procesarAcceso'])->name('procesarAcceso');
Route::post('/registrar', [LoginController::class, 'registrarUsuario'])->name('registrarUsuario');
Route::get('auth/{provider}', [LoginController::class, 'verProveedor'])->name('social.login');
Route::get('auth/{provider}/callback', [LoginController::class, 'manejarProveedor'])->name('social.callback');
Route::get('/home', [LoginController::class, 'home'])->name('home');
Route::post('/publicacion/crear', [LoginController::class, 'crearPublicacion'])->name('crear.publicacion');
Route::post('/publicacion/{id}/editar', [LoginController::class, 'editarPublicacion'])->name('editar.publicacion');
Route::delete('/publicacion/{id}/eliminar', [LoginController::class, 'eliminarPublicacion'])->name('eliminar.publicacion');
Route::post('/logout', [LoginController::class, 'cerrarSesion'])->name('logout');

// Rutas para búsqueda y perfiles
Route::get('/buscar', [LoginController::class, 'buscarUsuarios'])->name('buscar.usuarios');
Route::get('/perfil/{id}', [LoginController::class, 'verPerfil'])->name('ver.perfil');
Route::post('/perfil/actualizar', [LoginController::class, 'actualizarPerfil'])->name('actualizar.perfil');
Route::post('/perfil/foto', [LoginController::class, 'actualizarFoto'])->name('actualizar.foto');
Route::post('/actualizar-banner', [LoginController::class, 'actualizarBanner'])->name('actualizar.banner');

// Rutas para gestión de amistades
Route::post('/amistad/enviar', [LoginController::class, 'enviarSolicitudAmistad'])->name('enviar.solicitud');
Route::post('/amistad/aceptar', [LoginController::class, 'aceptarSolicitud'])->name('aceptar.solicitud');
Route::post('/amistad/rechazar', [LoginController::class, 'rechazarSolicitud'])->name('rechazar.solicitud');
Route::get('/amigos', [LoginController::class, 'verAmigos'])->name('ver.amigos');
Route::get('/solicitudes', [LoginController::class, 'verSolicitudesPendientes'])->name('ver.solicitudes');

// Rutas para el chat
Route::get('/amigos-mensajes', [LoginController::class, 'getAmigosConMensajes'])->name('amigos.mensajes');
Route::get('/total-mensajes-no-leidos', [LoginController::class, 'getTotalMensajesNoLeidos'])->name('mensajes.no.leidos');
Route::get('/chat/{amigo_id}', [LoginController::class, 'verChat'])->name('ver.chat');
Route::get('/chat/{amigo_id}/check-new', [LoginController::class, 'checkNewMessages'])->name('check.new.messages');
Route::post('/chat/enviar', [LoginController::class, 'enviarMensaje'])->name('enviar.mensaje');

// Rutas para likes
Route::post('/publicacion/{id}/like', [LoginController::class, 'darLike'])->name('dar.like');
Route::delete('/publicacion/{id}/like', [LoginController::class, 'quitarLike'])->name('quitar.like');
Route::post('/publicacion/editar/{id}', [LoginController::class, 'editar']);
Route::post('/publicacion/eliminar/{id}', [LoginController::class, 'eliminar']);

// Rutas para comentarios
Route::post('/publicacion/{id}/comentar', [LoginController::class, 'comentar'])->name('comentar');
Route::get('/publicacion/{id}/comentarios', [LoginController::class, 'obtenerComentarios'])->name('obtener.comentarios');

Route::post('/comentario/{id}/like', [LoginController::class, 'darLikeComentario']);
Route::delete('/comentario/{id}/like', [LoginController::class, 'quitarLikeComentario']);
Route::post('/comentario/{id}/editar', [LoginController::class, 'editarComentario'])->name('editar.comentario');
Route::delete('/comentario/{id}/eliminar', [LoginController::class, 'eliminarComentario'])->name('eliminar.comentario');

Route::get('resetcontra', [LoginController::class, 'showResetForm'])->name('password.request');
Route::post('password/email', [LoginController::class, 'sendResetLink'])->name('password.email');
Route::get('reset-password/{token}', [LoginController::class, 'showChangePasswordForm'])->name('password.reset');
Route::post('password/update', [LoginController::class, 'updatePassword'])->name('password.update');