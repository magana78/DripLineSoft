<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Usuario;


class ChangePasswordController extends Controller
{
    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request) // 🔥 Cambié el nombre del método aquí
    {
        $request->validate([
            'email' => 'required|email',
            'current_password' => 'required',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);
    
        $usuario = Auth::user();
    
        // Verificar que la contraseña actual sea correcta
        if (!Hash::check($request->current_password, $usuario->getAuthPassword())) {
            return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
        }
    
        $cambios = [];
    
        // Verificar si el correo cambió
        if ($request->email !== $usuario->email) {
            $cambios['email'] = $request->email;
        }
    
        // Verificar si se solicitó cambio de contraseña
        if ($request->filled('new_password')) {
            $cambios['contraseña'] = Hash::make($request->new_password);
        }
    
        // Si hay cambios, actualiza la información
        if (!empty($cambios)) {
            $usuario->update($cambios);
    
            if (isset($cambios['email']) && isset($cambios['contraseña'])) {
                return redirect()->route('password.change')->with('success', 'Correo y contraseña actualizados correctamente.');
            } elseif (isset($cambios['email'])) {
                return redirect()->route('password.change')->with('success', 'Correo actualizado correctamente.');
            } elseif (isset($cambios['contraseña'])) {
                return redirect()->route('password.change')->with('success', 'Contraseña actualizada correctamente.');
            }
        }
    
        return back()->with('info', 'No se realizaron cambios.');
    }
}
