<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductoResource;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        // Paginamos de 10 en 10 para no colapsar la memoria en catálogos gigantes
        $productos = Producto::orderBy('id', 'desc')->paginate(10);

        return response()->json([
            'status' => 'success',
            'data'   => ProductoResource::collection($productos),
            'pagination' => [
                'total'        => $productos->total(),
                'per_page'     => $productos->perPage(),
                'current_page' => $productos->currentPage(),
                'last_page'    => $productos->lastPage(),
            ]
        ], 200); // 200 OK
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('productos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $validados = $request->validate([
            'nombre'      => 'required|max:255',
            'precio' => 'required|numeric|min:0.01',
            'stock'       => 'required|integer|min:0',
        ]);

        $producto = Producto::create($validados);

        return response()->json(
            [
                'status' => 'created',
                'message' => 'Articulo ingresado exitosamente',
                'data' => new ProductoResource($producto)
            ], 201
        );
    }

    /**
     * Display the specified resource.
     */
     public function show(string $id): JsonResponse
    {
        // Buscamos el producto en MySQL
        $producto = Producto::find($id);

        // Si no existe, devolvemos error semántico 404
        if (!$producto) {
            return response()->json([
                'status'  => 'error',
                'message' => "El producto con ID #{$id} no existe en Ferretería La Bendición"
            ], 404); // 404 Not Found
        }

        return response()->json([
            'status' => 'success',
            'data'   => new ProductoResource($producto)
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        return view('productos.edit', compact('producto'));
         
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Producto no encontrado para actualizar'
            ], 404);
        }

        $validados = $request->validate([
            'nombre'       => 'required|string|max:150',
            'precio' => 'required|numeric|min:0.01',
            'stock'        => 'required|integer|min:0',
        ]);

        $producto->update($validados);

        return response()->json([
            'status'  => 'updated',
            'message' => 'Producto actualizado correctamente',
            'data'    => new ProductoResource($producto)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'status'  => 'error',
                'message' => 'El producto a eliminar no existe en bodega'
            ], 404);
        }

        $producto->delete();

        return response()->json([
            'status'  => 'deleted',
            'message' => 'Producto dado de baja exitosamente'
        ], 200);
    }
}

