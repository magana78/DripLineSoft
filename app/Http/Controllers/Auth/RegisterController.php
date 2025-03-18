<?php

namespace App\Http\Controllers\Auth;

use App\Models\Cliente;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:usuarios'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'nombre_comercial' => ['required', 'string', 'max:255'],
            'direccion' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:15'],
            'email_contacto' => ['required', 'string', 'email', 'max:255', 'unique:clientes,email_contacto'],
            'plan_suscripcion' => ['required', 'in:mensual,anual'],
            'sector' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'file', 'mimes:jpeg,png,jpg', 'max:2048']
        ]);
    }

    protected function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $usuario = Usuario::create([
                'nombre' => $data['nombre'],
                'email' => $data['email'],
                'contraseña' => Hash::make($data['password']),
                'rol' => 'admin_cliente',
                'fecha_creacion' => now(),
            ]);

            // ✅ Guardar imagen en storage y en la base de datos
            $logoPath = null;

            if (request()->hasFile('logo')) {
                if (!Storage::exists('public/logos')) {
                    Storage::makeDirectory('public/logos');
                }

                $file = request()->file('logo');
                $fileName = time() . '_' . $file->getClientOriginalName();
                Storage::disk('public')->putFileAs('logos', $file, $fileName); 
                $logoPath = 'logos/' . $fileName; 
            }

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
                'estado_suscripcion' => 'pendiente',
                'monto_suscripcion' => 0.00,
                'logo' => $logoPath
            ]);

            return $usuario;
        });
    }

    public function register(\Illuminate\Http\Request $request)
    {
        try {
            $this->validator($request->all())->validate();

            // 🔹 Verificar si el email_contacto ya existe (adicional)
            if (Cliente::where('email_contacto', $request->email_contacto)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'El correo electrónico de contacto ya está registrado.'
                ], 400);
            }

            $usuario = $this->create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Registro completado. Procede con el pago para activar la cuenta.',
                'email_contacto' => $request->email_contacto
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en el registro: ' . $e->getMessage()
            ], 500);
        }
    }
}
