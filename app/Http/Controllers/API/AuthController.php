<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//librerias 
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    /**
     * POST /api/login
     * Autentica al empleado de la ferretería y genera un Bearer Token.
     */
    public function login(Request $request): JsonResponse
    {
        // 1. Validamos formato de credenciales
        $request->validate([
            'email'       => 'required|email',
            'password'    => 'required|string'
        ]);

        // 2. Intentamos autenticar contra MySQL
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Credenciales inválidas en Ferretería La Bendición'
            ], 401); // 401 Unauthorized
        }

        // 3. Obtenemos el usuario y generamos el Token Sanctum
        $usuario = Auth::user();
        $dispositivo = $request->device_name ?? 'Dispositivo-Desconocido';
        $token = $usuario->createToken('DispositivoFerreteria')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Bienvenido al sistema de Ferretería La Bendición',
            'token'   => $token,
            'usuario' => [
                'id'    => $usuario->id,
                'name'  => $usuario->name,
                'email' => $usuario->email,
            ]
        ], 200);
    }
}
