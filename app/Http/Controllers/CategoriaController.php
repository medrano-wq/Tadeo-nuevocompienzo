<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function getCategorias() {
        $categorias = Categoria::all();
        return view("categoria", compact("categorias"));
    }

    public function createCategoria(Request $request) {
        $request->validate([
            "categoria" => "required|string|max:255|unique:categorias,categoria"
        ]);

        Categoria::create([
            "categoria" => $request->categoria
        ]);

        return redirect("/categorias")->with("mensaje", "Categoría registrada con éxito");
    }

    public function editCategoria($id) {
        $categoria = Categoria::findOrFail($id);
        $categorias = Categoria::all();
        return view("categoria", compact("categoria", "categorias"));
    }

    public function updateCategoria(Request $request, $id) {
        $request->validate([
            "categoria" => "required|string|max:255|unique:categorias,categoria," . $id
        ]);

        $categoria = Categoria::findOrFail($id);
        $categoria->update([
            "categoria" => $request->categoria
        ]);

        return redirect("/categorias")->with("mensaje", "Categoría actualizada con éxito");
    }

    public function deleteCategoria($id) {
        $categoria = Categoria::findOrFail($id);
        $categoria->delete();

        return redirect("/categorias")->with("mensaje", "Categoría eliminada con éxito");
    }
}