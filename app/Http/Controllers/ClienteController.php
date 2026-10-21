<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function getClientes() {
        $clientes = Cliente::all();
        return view("clientes", compact("clientes"));
    }

    public function createCliente(Request $request) {
        $request->validate([
            "nombre" => "required|string|max:255",
            "email" => "required|email|max:255|unique:clientes,email",
            "direccion" => "required|string|max:255",
            "telefono" => "required|string|max:50"
        ]);

        Cliente::create([
            "nombre" => $request->nombre,
            "email" => $request->email,
            "direccion" => $request->direccion,
            "telefono" => $request->telefono
        ]);

        return redirect("/clientes")->with("mensaje", "Cliente registrado con éxito");
    }

    public function editCliente($id) {
        $cliente = Cliente::findOrFail($id);
        $clientes = Cliente::all();
        return view("clientes", compact("cliente", "clientes"));
    }

    public function updateCliente(Request $request, $id) {
        $request->validate([
            "nombre" => "required|string|max:255",
            "email" => "required|email|max:255|unique:clientes,email," . $id,
            "direccion" => "required|string|max:255",
            "telefono" => "required|string|max:50"
        ]);

        $cliente = Cliente::findOrFail($id);
        $cliente->update([
            "nombre" => $request->nombre,
            "email" => $request->email,
            "direccion" => $request->direccion,
            "telefono" => $request->telefono
        ]);

        return redirect("/clientes")->with("mensaje", "Cliente actualizado con éxito");
    }

    public function deleteCliente($id) {
        $cliente = Cliente::findOrFail($id);
        $cliente->delete();

        return redirect("/clientes")->with("mensaje", "Cliente eliminado con éxito");
    }
}