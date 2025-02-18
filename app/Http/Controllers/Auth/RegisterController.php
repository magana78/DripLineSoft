<?php

namespace App\Http\Controllers\Auth;

use App\Models\Cliente;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Redirigir al dashboard después de registrarse.
     */
    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Validar los datos del formulario de registro.
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:usuarios'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'rol' => ['required', 'string'],
            'nombre_comercial' => ['required', 'string', 'max:255'],
            'direccion' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:15'],
            'email_contacto' => ['required', 'string', 'email', 'max:255'],
            'plan_suscripcion' => ['required', 'in:mensual,anual'], // Solo valores válidos
            'sector' => ['required', 'string', 'max:255'],
        ]);
    }

    /**
     * Crear una nueva instancia de usuario y cliente después de un registro válido.
     */
    protected function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Registrar usuario
            $usuario = Usuario::create([
                'nombre' => $data['nombre'],
                'email' => $data['email'],
                'contraseña' => Hash::make($data['password']),
                'rol' => $data['rol'],
                'fecha_creacion' => now(),
            ]);
    
            // Registrar negocio, pero sin activar la suscripción aún
            Cliente::create([
                'id_usuario' => $usuario->id_usuario,
                'nombre_comercial' => $data['nombre_comercial'],
                'direccion' => $data['direccion'],
                'telefono' => $data['telefono'],
                'email_contacto' => $data['email_contacto'],
                'plan_suscripcion' => $data['plan_suscripcion'],
                'fecha_registro' => now(),
                'fecha_fin_suscripcion' => now()->addDays(30),
                'sector' => $data['sector'],
                'estado_suscripcion' => 'pendiente', // 🔹 Queda pendiente hasta que pague
                'monto_suscripcion' => 0.00 // 🔹 Aún no ha pagado
            ]);
    
            return $usuario;
        });
    }
    

    /**
     * Sobrescribimos el método `register` para autenticar al usuario y redirigirlo.
     */
    public function register(\Illuminate\Http\Request $request)
    {
        // Validar los datos del formulario
        $this->validator($request->all())->validate();
    
        // Crear el usuario y el cliente asociado
        $usuario = $this->create($request->all());
    
        return response()->json([
            'success' => true,
            'message' => 'Registro completado. Procede con el pago para activar la cuenta.',
            'email_contacto' => $request->email_contacto // 💡 Guardamos el email para identificarlo en el pago
        ]);
    }
    
}
