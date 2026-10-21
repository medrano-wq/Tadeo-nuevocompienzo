<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cliente;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    public function getVentas() {
        $ventas = Venta::with('cliente')->get();
        $clientes = Cliente::all();
        return view("ventas", compact("ventas", "clientes"));
    }

    public function createVenta(Request $request) {
        $request->validate([
            "cliente_id" => "required|exists:clientes,id",
            "total" => "required|numeric|min:0"
        ]);

        Venta::create([
            "cliente_id" => $request->cliente_id,
            "total" => $request->total
        ]);

        return redirect("/ventas")->with("mensaje", "Venta registrada con éxito");
    }

    public function editVenta($id) {
        $venta = Venta::findOrFail($id);
        $ventas = Venta::with('cliente')->get();
        $clientes = Cliente::all();
        return view("ventas", compact("venta", "ventas", "clientes"));
    }

    public function updateVenta(Request $request, $id) {
        $request->validate([
            "cliente_id" => "required|exists:clientes,id",
            "total" => "required|numeric|min:0"
        ]);

        $venta = Venta::findOrFail($id);
        $venta->update([
            "cliente_id" => $request->cliente_id,
            "total" => $request->total
        ]);

        return redirect("/ventas")->with("mensaje", "Venta actualizada con éxito");
    }

    public function deleteVenta($id) {
        $venta = Venta::findOrFail($id);
        $venta->delete();

        return redirect("/ventas")->with("mensaje", "Venta eliminada con éxito");
    }
}