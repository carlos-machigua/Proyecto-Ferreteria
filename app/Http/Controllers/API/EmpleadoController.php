<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmpleadoResource;
use App\Models\Empleado;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        // Paginamos de 10 en 10 para no colapsar la memoria en catálogos gigantes
        $empleado = Empleado::orderBy('id', 'desc')->paginate(10);

        return response()->json([
            'status' => 'success',
            'data'   => EmpleadoResource::collection($empleado),
            'pagination' => [
                'total'        => $empleado->total(),
                'per_page'     => $empleado->perPage(),
                'current_page' => $empleado->currentPage(),
                'last_page'    => $empleado->lastPage(),
            ]
        ], 200); // 200 OK
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('empleado.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $validados = $request->validate([
            'identidad'      => 'required|max:13',
            'nombre'      => 'required|max:255',
            'cargo'       => 'required|max:255',
            'salario' => 'required|numeric|min:0.01',
            'codigo_empleado'      => 'required|max:7',
        ]);

        $empleado = Empleado::create($validados);

        return response()->json(
            [
                'status' => 'created',
                'message' => 'Articulo ingresado exitosamente',
                'data' => new EmpleadoResource($empleado)
            ], 201
        );
    }

    /**
     * Display the specified resource.
     */
     public function show(string $id): JsonResponse
    {
        // Buscamos el empleado en MySQL
        $empleado = Empleado::find($id);

        // Si no existe, devolvemos error semántico 404
        if (!$empleado) {
            return response()->json([
                'status'  => 'error',
                'message' => "El empleado con ID #{$id} no existe en Ferretería La Bendición"
            ], 404); // 404 Not Found
        }

        return response()->json([
            'status' => 'success',
            'data'   => new EmpleadoResource($empleado)
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Empleado $empleado)
    {
        return view('empleados.edit', compact('empleado'));
         
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $empleado = Empleado::find($id);

        if (!$empleado) {
            return response()->json([
                'status'  => 'error',
                'message' => 'empleado no encontrado para actualizar'
            ], 404);
        }

        $validados = $request->validate([
            'identidad'      => 'required|max:13',
            'nombre'      => 'required|max:255',
            'cargo'       => 'required|max:255',
            'salario' => 'required|numeric|min:0.01',
            'codigo_empleado'      => 'required|max:7',
        ]);

        $empleado->update($validados);

        return response()->json([
            'status'  => 'updated',
            'message' => 'Empleado actualizado correctamente',
            'data'    => new EmpleadoResource($empleado)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $empleado = Empleado::find($id);

        if (!$empleado) {
            return response()->json([
                'status'  => 'error',
                'message' => 'El empleado a eliminar no existe en la base de datos.'
            ], 404);
        }

        $empleado->delete();

        return response()->json([
            'status'  => 'deleted',
            'message' => 'Empleado dado de baja exitosamente'
        ], 200);
    }
}
