<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    public function index()
    {
        $empleados = Empleado::paginate(10); 
        return view('empleados.index', compact('empleados')); 
    }

    public function create()
    {
        return view('empleados.create');
    }

    public function store(Request $request)
    { 
        $request->validate([
            'identidad'       => 'required|string|size:13|unique:empleados,identidad',
            'nombre'          => 'required|string|max:255',
            'cargo'           => 'required|string|max:255',
            'salario'         => 'required|numeric|min:0',
            'codigo_empleado' => 'nullable|string|max:50|unique:empleados,codigo_empleado',
            'fecha_ingreso'   => 'nullable|date',
        ]);

        Empleado::create($request->all());

        return redirect()->route('empleados.index')->with('success', 'Empleado creado exitosamente.');
    }

    public function show(Empleado $empleado)
    {
        return view('empleados.show', compact('empleado'));
    }

    public function edit(Empleado $empleado)
    {
        return view('empleados.edit', compact('empleado'));
    }

    public function update(Request $request, Empleado $empleado)
    {
        $request->validate([
            'identidad'       => 'required|string|size:13|unique:empleados,identidad,' . $empleado->id,
            'nombre'          => 'required|string|max:255',
            'cargo'           => 'required|string|max:255',
            'salario'         => 'required|numeric|min:0',
            'codigo_empleado' => 'nullable|string|max:50|unique:empleados,codigo_empleado,' . $empleado->id,
            'fecha_ingreso'   => 'nullable|date',
        ]);

        $empleado->update($request->all());

        return redirect()->route('empleados.index')->with('success', 'Empleado actualizado exitosamente.');
    }

    public function destroy(Empleado $empleado)
    {
        $empleado->delete(); 

        return redirect()->route('empleados.index')->with('success', 'Empleado eliminado correctamente.');
    }
}


