<?php
namespace App\Http\Controllers\Network;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Exception;

class LoginController extends Controller
{
    public function acceso() 
    { 
        return view('acceso'); 
    }

    public function verificarSesion()
    {
        if (!session('user_id') || !session('user_name') || !session('user_email')) {
            return redirect()->route('acceso')
                ->withErrors('Por favor, complete el registro o inicie sesión primero.');
        }
        return true;
    }

    public function home()
    {
        $verificacion = $this->verificarSesion();
        if ($verificacion !== true) {
            return $verificacion;
        }

        $userId = session('user_id');
        $amigosIds = DB::select('CALL sp_Amigo_VerAmigos(?)', [$userId]);
        $ids = array_column($amigosIds, 'amigo_id');
        $ids[] = $userId;
        $publicaciones = DB::select('CALL sp_Publicacion_ObtenerFeed(?)', [implode(',', $ids)]);

        return view('home', ['publicaciones' => $publicaciones]);
    }
    
    public function crearPublicacion(Request $request)
    {
        $request->validate([
            'contenido' => 'required|string|max:1000',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $resultado = DB::select('CALL sp_Publicacion_Crear(?, ?)', [
            session('user_id'),
            $request->contenido
        ]);
        
        $publicacionId = $resultado[0]->publicacion_id;

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $index => $imagen) {
                $ruta = $imagen->store('publicaciones', 'public');
                
                DB::select('CALL sp_Publicacion_AgregarImagen(?, ?, ?)', [
                    $publicacionId, $ruta, $index
                ]);
            }
        }
        return redirect()->route('home')->with('success', 'Publicación creada exitosamente.');
    }

    public function editarPublicacion(Request $request, $id)
    {
        $request->validate([
            'contenido' => 'required|string|max:1000'
        ]);

        $usuarioId = session('user_id');
        $publicacion = DB::connection('mysql')->select('CALL sp_Publicacion_ObtenerPorId(?)', [$id]);

        if ($publicacion && $publicacion[0]->usuario_id == $usuarioId) {
            DB::connection('mysql')->select('CALL sp_Publicacion_Editar(?, ?)', [$id, $request->contenido]);
            return redirect()->route('home')->with('success', 'Publicación actualizada correctamente.');
        } else {
            return redirect()->route('home')->withErrors('No tienes permiso para editar esta publicación.');
        }
    }

    public function eliminarPublicacion($id)
    {
        $usuarioId = session('user_id');
        $publicacion = DB::connection('mysql')->select('CALL sp_Publicacion_ObtenerPorId(?)', [$id]);

        if ($publicacion && $publicacion[0]->usuario_id == $usuarioId) {
            DB::connection('mysql')->select('CALL sp_Publicacion_Eliminar(?)', [$id]);
            return redirect()->route('home')->with('success', 'Publicación eliminada correctamente.');
        } else {
            return redirect()->route('home')->withErrors('No tienes permiso para eliminar esta publicación.');
        }
    }

    public function darLike($id)
    {
        try {
            DB::select('CALL sp_Like_Crear(?, ?)', [session('user_id'), $id]);
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ya has dado like a esta publicación'], 400);
        }
    }

    public function quitarLike($id)
    {
        DB::select('CALL sp_Like_Eliminar(?, ?)', [session('user_id'), $id]);
        return response()->json(['success' => true]);
    }

    public function comentar(Request $request, $id)
    {
        $request->validate([
            'contenido' => 'required|string|max:500'
        ]);

        $comentario = DB::select('CALL sp_Comentario_Crear(?, ?, ?)', [
            session('user_id'),
            $id,
            $request->contenido
        ]);

        return response()->json([
            'success' => true,
            'comentario' => $comentario[0]
        ]);
    }

    public function darLikeComentario($id)
    {
        try {
            DB::select('CALL sp_ComentarioLike_Crear(?, ?)', [session('user_id'), $id]);
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ya has dado like a este comentario'], 400);
        }
    }

    public function quitarLikeComentario($id)
    {
        DB::select('CALL sp_ComentarioLike_Eliminar(?, ?)', [session('user_id'), $id]);
        return response()->json(['success' => true]);
    }

    public function obtenerComentarios($id)
    {
        $comentarios = DB::select('CALL sp_Comentario_ObtenerPorPublicacion(?, ?)', [
            $id,
            session('user_id')
        ]);
        return response()->json($comentarios);
    }

    public function editarComentario(Request $request, $id)
    {
        $request->validate([
            'contenido' => 'required|string|max:500'
        ]);

        $usuarioId = session('user_id');
        $comentario = DB::select('CALL sp_Comentario_ObtenerPorId(?)', [$id]);

        if ($comentario && $comentario[0]->usuario_id == $usuarioId) {
            $comentarioActualizado = DB::select('CALL sp_Comentario_Editar(?, ?)', [
                $id, 
                $request->contenido
            ]);
            return response()->json($comentarioActualizado[0]);
        }
        
        return response()->json(['error' => 'No autorizado'], 403);
    }

    public function eliminarComentario($id)
    {
        $usuarioId = session('user_id');
        $comentario = DB::select('CALL sp_Comentario_ObtenerPorId(?)', [$id]);

        if ($comentario && $comentario[0]->usuario_id == $usuarioId) {
            DB::select('CALL sp_Comentario_Eliminar(?)', [$id]);
            return response()->json(['success' => true]);
        }
        
        return response()->json(['error' => 'No autorizado'], 403);
    }

    private function checkEmailExists($email) 
    {
        return DB::connection('mysql')
            ->table('usuario')
            ->where('correo', $email)
            ->first();
    }

    public function procesarAcceso(Request $request)
    {
        $existingUser = DB::connection('mysql')
            ->table('usuario')
            ->select('provider', 'nombres', 'id')
            ->where('correo', $request->email)
            ->first();
    
        if (!$existingUser) {
            return redirect()->route('acceso')
                ->withErrors('El correo electrónico no está registrado. Por favor, regístrese primero.')
                ->withInput(['email' => $request->email]);
        }

        if ($existingUser && $existingUser->provider !== 'email') {
            return redirect()->route('acceso')
                ->withErrors('Este correo ya está registrado con ' . ucfirst($existingUser->provider) . 
                    '. Por favor, usa ese método para iniciar sesión.')
                ->withInput(['email' => $request->email]);
        }

        if ($user = DB::connection('mysql')->select('CALL sp_Usuario_Login(?, ?)', 
            [$request->email, $request->password])) {

            session(['user_name' => $user[0]->nombres, 'user_id' => $user[0]->id, 'user_email' => $request->email]);

            return redirect()->route('home')
                ->with('status', '¡Bienvenido ' . $user[0]->nombres . '!');
        }

        return redirect()->route('acceso')
            ->withErrors('Contraseña incorrecta.')
            ->withInput(['email' => $request->email]);
    }    

    public function registrarUsuario(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:usuario,correo',
            'username' => 'required|string|max:100',
            'password' => 'required|string',
        ]);

        $existingUser = DB::connection('mysql')
            ->table('usuario')
            ->select('provider', 'nombres')
            ->where('correo', $request->email)
            ->first();

        if ($existingUser && $existingUser->provider !== 'email') {
            return redirect()->route('acceso')
                ->withErrors('Este correo ya está registrado con ' . ucfirst($existingUser->provider) . 
                    '. Por favor, usa ese método para iniciar sesión.')
                ->withInput(['email' => $request->email]);
        }

        if ($existingUser && $existingUser->provider === 'email') {
            return redirect()->route('acceso')
                ->withErrors('Este correo ya está registrado. Por favor, inicia sesión o usa otro correo.')
                ->withInput(['email' => $request->email]);
        }

        $password = $request->password;

        DB::connection('mysql')->select('CALL sp_Usuario_Guardar(?, ?, ?, ?)', 
            [$request->username, $request->email, $password, 'email']);

        $newUser = DB::connection('mysql')
            ->table('usuario')
            ->where('correo', $request->email)
            ->first();

        session([
            'user_id' => $newUser->id,
            'user_name' => $request->username,
            'user_email' => $request->email,
        ]);
    
        return redirect()->route('home')
            ->with('status', '¡Bienvenido ' . $request->username . '!');
    }    

    public function cerrarSesion(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('acceso')
            ->withErrors('Sesión cerrada exitosamente.');
    }

    public function verProveedor($provider)
    {
        try {
            return Socialite::driver($provider)->redirect();
        } catch (Exception $e) {
            return redirect()->route('acceso')
                ->withErrors('Error al conectar con ' . $provider);
        }
    }

    public function manejarProveedor($provider)
    {
        try {
            $user = Socialite::driver($provider)->user();
            $email = $user->getEmail();
            $name = $user->getName() ?? $user->getNickname();

            $existingUser = DB::connection('mysql')
                ->table('usuario')
                ->select('id', 'provider', 'nombres')
                ->where('correo', $email)
                ->first();

            if ($existingUser) {
                if ($existingUser->provider !== $provider) {
                    return redirect()->route('acceso')
                        ->withErrors('Este correo ya está registrado con ' . 
                            ucfirst($existingUser->provider) . 
                            '. Por favor, usa ese método para iniciar sesión.');
                }

                session(['user_name' => $name, 'user_id' => $existingUser->id, 'user_email' => $email]);
                return redirect()->route('home')
                    ->withErrors('¡Bienvenido ' . $existingUser->nombres . '!');
            }

            DB::connection('mysql')->select('CALL sp_Usuario_Guardar(?, ?, ?, ?)', 
                [$name, $email, Str::random(16), $provider]);

            $newUserId = DB::connection('mysql')
                ->table('usuario')
                ->where('correo', $email)
                ->value('id');

            session(['user_name' => $name, 'user_id' => $newUserId, 'user_email' => $email]);

            return redirect()->route('home')
                ->withErrors('¡Bienvenido ' . $name . '!');

        } catch (Exception $e) {
            return redirect()->route('acceso')
                ->withErrors('Error al iniciar sesión con ' . $provider . 
                    '. Por favor, intenta nuevamente.');
        }
    }

    public function buscarUsuarios(Request $request)
    {
        $usuarioId = session('user_id'); 

        $usuarios = DB::connection('mysql')
            ->select('CALL sp_Usuario_Buscar(?, ?)', 
            [$request->input('busqueda'), $usuarioId]);
        
        return response()->json($usuarios);
    }

    public function verPerfil($id)
    {
        $verificacion = $this->verificarSesion();
        if ($verificacion !== true) {
            return $verificacion;
        }

        $perfil = DB::connection('mysql')
            ->table('usuario')
            ->where('id', $id)
            ->first();
        
        if (!$perfil) {
            return redirect()->route('home')
                ->withErrors('Usuario no encontrado.');
        }
    
        $estadoAmistad = DB::connection('mysql')
            ->select('CALL sp_Amigo_VerificarAmistad(?, ?)', 
            [session('user_id'), $id]);
    
        return view('perfil', [
            'usuario' => $perfil,
            'estadoAmistad' => !empty($estadoAmistad) ? $estadoAmistad[0] : null
        ]);
    }

    public function actualizarPerfil(Request $request)
    {
        $userId = session('user_id');
        
        $datos = $request->only([
            'descripcion',
            'fecha_nacimiento',
            'centro_estudios',
            'centro_trabajo',
            'genero',
            'ubicacion',
            'ocupacion',
            'intereses'
        ]);

        $datos = array_filter($datos, function($value) {
            return $value !== null && $value !== '';
        });
        
        try {
            DB::connection('mysql')->table('usuario')
                ->where('id', $userId)
                ->update($datos);
                
            return redirect()->back()->with('success', 'Perfil actualizado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error al actualizar el perfil');
        }
    }

    public function actualizarFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|max:2048'
        ]);
    
        $foto = $request->file('foto');
        $nombreArchivo = 'user_' . session('user_id') . '.' . $foto->getClientOriginalExtension();
        $rutaAnterior = public_path('storage/fotos_perfil/' . $nombreArchivo);
        if (file_exists($rutaAnterior)) {
            unlink($rutaAnterior);
        }

        $foto->move(public_path('storage/fotos_perfil'), $nombreArchivo);
        DB::connection('mysql')->select(
            'CALL sp_Usuario_ActualizarFoto(?, ?)', 
            [session('user_id'), $nombreArchivo]
        );
        
        return back()->with('success', 'Foto de perfil actualizada correctamente');
    }

    public function actualizarBanner(Request $request)
    {
        $request->validate([
            'banner' => 'required|image|max:5120'
        ]);

        $banner = $request->file('banner');
        $nombreArchivo = 'banner_' . session('user_id') . '.' . $banner->getClientOriginalExtension();

        $rutaAnterior = public_path('storage/banners/' . $nombreArchivo);
        if (file_exists($rutaAnterior)) {
            unlink($rutaAnterior);
        }

        $banner->move(public_path('storage/banners'), $nombreArchivo);
        DB::connection('mysql')->select(
            'CALL sp_Usuario_ActualizarBanner(?, ?)', 
            [session('user_id'), $nombreArchivo]
        );

        return back()->with('success', 'Banner actualizado correctamente');
    }

    public function enviarSolicitudAmistad(Request $request)
    {
        $request->validate([
            'amigo_id' => 'required|integer|exists:usuario,id',
        ]);

        $userId = session('user_id');
        $amigoId = $request->input('amigo_id');

        try {
            DB::connection('mysql')->select('CALL sp_Amigo_EnviarSolicitud(?, ?)', 
                [$userId, $amigoId]);

            return redirect()->back()->with('success', 'Solicitud de amistad enviada.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors('Error al enviar la solicitud: ' . $e->getMessage());
        }
    }

    public function verSolicitudes()
    {
        $verificacion = $this->verificarSesion();
        if ($verificacion !== true) {
            return $verificacion;
        }

        $userId = session('user_id');
        $solicitudes = DB::connection('mysql')->select('CALL sp_ObtenerSolicitudes(?)', [$userId]);

        return view('solicitudes', ['solicitudes' => $solicitudes]);
    }

    public function verSolicitudesPendientes()
    {
        $verificacion = $this->verificarSesion();
        if ($verificacion !== true) {
            return $verificacion;
        }

        $userId = session('user_id');
        $solicitudes = DB::connection('mysql')
            ->select('CALL sp_Amigo_VerSolicitudesPendientes(?)', [$userId]);
        $pendingRequests = DB::connection('mysql')
            ->select('CALL sp_Amigo_ContarSolicitudesPendientes(?)', [$userId]);
        
        return view('solicitudes', [
            'solicitudes' => $solicitudes,
            'pendingRequests' => $pendingRequests[0]->solicitudes_pendientes ?? 0
        ]);
    }

    public function __construct()
    {
        if (session()->has('user_id')) {
            $pendingRequests = DB::connection('mysql')
                ->select('CALL sp_Amigo_ContarSolicitudesPendientes(?)', 
                [session('user_id')]);

            view()->share('pendingRequests', $pendingRequests[0]->solicitudes_pendientes ?? 0);
        } else {
            view()->share('pendingRequests', 0);
        }
    }

    public function aceptarSolicitud(Request $request)
    {
        DB::connection('mysql')
            ->select('CALL sp_Amigo_AceptarSolicitud(?, ?)', 
            [session('user_id'), $request->amigo_id]);
        return redirect()->back()
            ->withErrors('Solicitud de amistad aceptada.');
    }

    public function rechazarSolicitud(Request $request)
    {
        $request->validate([
            'amigo_id' => 'required|integer',
        ]);
        $userId = session('user_id');
        $amigoId = $request->input('amigo_id');

        try {
            DB::connection('mysql')->select('CALL sp_Amigo_RechazarSolicitud(?, ?)', 
                [$userId, $amigoId]);

            return redirect()->route('ver.solicitudes')->with('success', 'Solicitud rechazada.');
        } catch (Exception $e) {
            return redirect()->route('ver.solicitudes')->withErrors('Error al rechazar la solicitud: ' . $e->getMessage());
        }
    }

    private function getPendingRequestsCount()
    {
        if (!session()->has('user_id')) {
            return 0;
        }
        
        $pendingRequests = DB::connection('mysql')
            ->select('CALL sp_Amigo_ContarSolicitudesPendientes(?)', 
            [session('user_id')]);
            
        return $pendingRequests[0]->solicitudes_pendientes ?? 0;
    }

    public function verAmigos()
    {
        $verificacion = $this->verificarSesion();
        if ($verificacion !== true) {
            return $verificacion;
        }

        $amigos = DB::connection('mysql')
            ->select('CALL sp_Amigo_VerAmigos(?)', 
            [session('user_id')]);
        return view('amigos', ['amigos' => $amigos]);
    }

    public function verChat($amigo_id)
    {
        $verificacion = $this->verificarSesion();
        if ($verificacion !== true) {
            return $verificacion;
        }

        $mensajes = DB::connection('mysql')
            ->select('CALL sp_Mensaje_VerConversacion(?, ?)', 
            [session('user_id'), $amigo_id]);
        $amigo = DB::connection('mysql')
            ->select('CALL sp_Usuario_VerPerfil(?)', 
            [$amigo_id]);
        return view('chat', [
            'mensajes' => $mensajes,
            'amigo' => $amigo[0]
        ]);
    }

    public function enviarMensaje(Request $request)
    {
        $request->validate([
            'contenido' => 'required|string',
            'amigo_id' => 'required|integer',
        ]);

        DB::statement('CALL sp_Mensaje_Enviar(?, ?, ?)', [
            session('user_id'),
            $request->amigo_id,
            $request->contenido,
        ]);
        return response()->json([
            'contenido' => $request->contenido,
            'fecha' => now()->format('H:i')
        ]);
    }

    public function checkNewMessages(Request $request, $amigo_id)
    {
        $usuario_id = session('user_id');
        $last_id = $request->input('last_id', 0);
    
        DB::update("
            UPDATE mensaje 
            SET leido = 1
            WHERE id <= ?
            AND usuario_id = ?
            AND amigo_id = ?
        ", [$last_id, $amigo_id, $usuario_id]);
    
        $nuevos_mensajes = DB::select("
            SELECT m.* 
            FROM mensaje m
            WHERE m.id > ?
            AND ((m.usuario_id = ? AND m.amigo_id = ?)
            OR (m.usuario_id = ? AND m.amigo_id = ?))
            ORDER BY m.fecha ASC
        ", [$last_id, $usuario_id, $amigo_id, $amigo_id, $usuario_id]);
    
        foreach ($nuevos_mensajes as $mensaje) {
            $mensaje->fecha = \Carbon\Carbon::parse($mensaje->fecha)->format('H:i');
        }
    
        return response()->json(['mensajes' => $nuevos_mensajes]);
    }
    
    public function getAmigosConMensajes()
    {
        $amigos = DB::select("
            SELECT 
                u.id,
                u.nombres,
                u.foto_perfil,
                COALESCE(COUNT(m.id), 0) as mensajes_nuevos
            FROM usuario u
            INNER JOIN amigo a ON (
                (a.usuario_id = ? AND a.amigo_id = u.id) OR 
                (a.amigo_id = ? AND a.usuario_id = u.id)
            )
            LEFT JOIN mensaje m ON (
                m.usuario_id = u.id AND 
                m.amigo_id = ? AND 
                m.leido = 0
            )
            WHERE a.estado = 'aceptado'
            GROUP BY u.id, u.nombres, u.foto_perfil
            ORDER BY mensajes_nuevos DESC, u.nombres ASC
        ", [session('user_id'), session('user_id'), session('user_id')]);
    
        foreach ($amigos as $amigo) {
            if ($amigo->foto_perfil) {
                $amigo->foto_perfil = '/storage/fotos_perfil/' . $amigo->foto_perfil;
            } else {
                $amigo->foto_perfil = '/img/default.jpg';
            }
        }
    
        return response()->json($amigos);
    }    

    public function getTotalMensajesNoLeidos()
    {
        $total = DB::selectOne("
            SELECT COUNT(*) as total
            FROM mensaje
            WHERE amigo_id = ? 
            AND leido = 0
        ", [session('user_id')]);

        return response()->json(['total' => $total->total]);
    }

    public function showResetForm()
    {
        return view('resetcontra');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'correo' => 'required|email'
        ]);

        $existingUser = DB::connection('mysql')
            ->table('usuario')
            ->select('provider', 'nombres')
            ->where('correo', $request->correo)
            ->first();

        if (!$existingUser) {
            return back()->withErrors('El correo electrónico no está registrado.')
                        ->withInput(['correo' => $request->correo]);
        }

        if ($existingUser->provider !== 'email') {
            return back()->withErrors('Este correo está registrado con ' . ucfirst($existingUser->provider) . 
                '. No se puede restablecer la contraseña.')
                ->withInput(['correo' => $request->correo]);
        }

        $token = Str::random(60);

        // Primero eliminamos cualquier token existente para este correo
        DB::connection('mysql')
            ->table('password_resets')
            ->where('correo', $request->correo)
            ->delete();

        // Insertamos el nuevo token
        DB::connection('mysql')
            ->table('password_resets')
            ->insert([
                'correo' => $request->correo,
                'token' => $token,
                'created_at' => now()
            ]);

        $resetUrl = url("/reset-password/{$token}?email=" . urlencode($request->correo));
        
        // Enviar el correo
        Mail::send('emails.reset-password', ['resetUrl' => $resetUrl, 'nombres' => $existingUser->nombres], 
            function($message) use ($request, $existingUser) {
                $message->to($request->correo);
                $message->subject('Recuperación de Contraseña');
            });

        return back()->with('status', 'Te hemos enviado un correo con las instrucciones para recuperar tu contraseña.');
    }

    public function showChangePasswordForm($token)
    {
        $correo = request('email');
        
        $tokenData = DB::connection('mysql')
            ->table('password_resets')
            ->where('correo', $correo)
            ->where('token', $token)
            ->where('created_at', '>', now()->subHours(1))
            ->first();

        if (!$tokenData) {
            return redirect()->route('password.request')
                ->withErrors('El enlace para restablecer la contraseña es inválido o ha expirado.');
        }

        return view('cambiar-password', compact('token', 'correo'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'correo' => 'required|email',
            'password' => 'required|confirmed',
        ]);
    
        // Verificar si el token es válido y no ha expirado
        $tokenData = DB::connection('mysql')
            ->table('password_resets')
            ->where('correo', $request->correo)
            ->where('token', $request->token)
            ->where('created_at', '>', now()->subHours(1))
            ->first();
    
        if (!$tokenData) {
            return back()->withErrors('El enlace para restablecer la contraseña es inválido o ha expirado.');
        }
    
        // Omitir la encriptación y usar la contraseña tal cual
        $password = $request->password;  // Usar la contraseña tal cual, sin encriptar
    
        // Actualizar la contraseña usando el stored procedure
        DB::connection('mysql')->select('CALL sp_Usuario_UpdatePassword(?, ?)', 
            [$request->correo, $password]);
    
        // Eliminar el token usado para evitar que se reutilice
        DB::connection('mysql')
            ->table('password_resets')
            ->where('correo', $request->correo)
            ->delete();
    
        return redirect()->route('acceso')
            ->with('status', '¡Tu contraseña ha sido actualizada exitosamente!');
    }    
}