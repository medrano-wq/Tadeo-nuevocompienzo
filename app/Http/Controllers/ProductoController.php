<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;

class ProductoController extends Controller
{
    public function getProductos() {
        $productos = Producto::all();
        $categorias = Categoria::all();
        return view("productos", compact("productos", "categorias"));
    }

    public function createProducto(Request $request) {
        $request->validate([
            "nombre" => "required|string|max:255",
            "precio" => "required|numeric",
            "stock" => "required|integer",
            "categoria_id" => "required|exists:categorias,id"
        ]);

        Producto::create([
            "nombre" => $request->nombre,
            "precio" => $request->precio,
            "stock" => $request->stock,
            "categoria_id" => $request->categoria_id
        ]);

        return redirect("/productos")->with("mensaje", "Producto registrado con éxito");
    }

    public function editProducto($id) {
        $producto = Producto::find($id);
        $productos = Producto::all();
        $categorias = Categoria::all();
        return view("productos", compact("producto", "productos", "categorias"));
    }

    public function updateProducto($id, Request $request) {
        $request->validate([
            "nombre" => "required|string|max:255",
            "precio" => "required|numeric",
            "stock" => "required|integer",
            "categoria_id" => "required|exists:categorias,id"
        ]);

        $producto = Producto::find($id);
        if ($producto) {
            $producto->update([
                "nombre" => $request->nombre,
                "precio" => $request->precio,
                "stock" => $request->stock,
                "categoria_id" => $request->categoria_id
            ]);
        }

        return redirect("/productos")->with("mensaje", "Producto actualizado con éxito");
    }

    public function deleteProducto($id) {
        $producto = Producto::find($id);
        if ($producto) {
            $producto->delete();
        }

        return redirect("/productos")->with("mensaje", "Producto eliminado con éxito");
    }
}