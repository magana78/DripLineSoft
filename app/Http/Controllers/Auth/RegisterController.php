<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Usuario;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
            'email_contacto' => ['required', 'string', 'email'],
            'plan_suscripcion' => ['required', 'string'],
            'monto_suscripcion' => ['required', 'numeric'],
            'fecha_registro' => ['required', 'date'],
            'fecha_fin_suscripcion' => ['required', 'date'],
            'estado_suscripcion' => ['required', 'string'],
            'sector' => ['required', 'string', 'max:255'],
        ]);
    }

    /**
     * Crear una nueva instancia de usuario y cliente después de un registro válido.
     */
    protected function create(array $data)
    {
        // Crear el usuario en la tabla 'usuarios'
        $usuarios = Usuario::create([
            'nombre' => $data['nombre'],
            'email' => $data['email'],
            'contraseña' => Hash::make($data['password']),
            'rol' => $data['rol'],
            'fecha_creacion' => now(),
        ]);

        // Crear el cliente en la tabla 'clientes'
        Cliente::create([
            'id_usuario' => $usuarios->id_usuario,  // Correcto si la clave primaria es id_usuario
            'nombre_comercial' => $data['nombre_comercial'],
            'direccion' => $data['direccion'],
            'telefono' => $data['telefono'],
            'email_contacto' => $data['email_contacto'],
            'plan_suscripcion' => $data['plan_suscripcion'],
            'monto_suscripcion' => $data['monto_suscripcion'],
            'fecha_registro' => $data['fecha_registro'],
            'fecha_fin_suscripcion' => $data['fecha_fin_suscripcion'],
            'estado_suscripcion' => $data['estado_suscripcion'],
            'sector' => $data['sector'],
        ]);

        return $usuarios;
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

        // Autenticar al usuario
        Auth::guard('web')->login($usuario);

        // Redirigir al dashboard
        return redirect($this->redirectTo);
    }
}
