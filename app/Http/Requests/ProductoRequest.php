<?php

namespace App\Http\Requests;

use App\Models\Producto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductoRequest extends FormRequest
{
    // si se autoriza o nell
    public function authorize(): bool
    {
        return true;
    }
// el metodo nos cuenta que se van al realizar las validaciones que va hacer cuando producto tenga 
// si no retorna con el metodo messages el error o lo que falta para mandarlo
    public function rules(): array
    {
        /** @var Producto|null $producto */
        $producto = $this->route('producto');

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('productos', 'nombre')->ignore($producto?->id),
            ],
            'descripcion' => ['required', 'string'],
            'categoria_id' => ['required', 'integer', 'exists:categorias,id'],
            'precio' => ['required', 'numeric', 'gt:0'],
            'existencias' => ['required', 'integer', 'min:0'],
            'estado' => ['required', 'string', Rule::in(['Disponible', 'Agotado'])],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.unique' => 'Ya existe un producto registrado con ese nombre.',
            'descripcion.required' => 'La descripción del producto es obligatoria.',
            'categoria_id.required' => 'Debes seleccionar una categoría.',
            'categoria_id.exists' => 'La categoría seleccionada no existe en el catálogo.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un valor numérico.',
            'precio.gt' => 'El precio debe ser mayor que cero.',
            'existencias.required' => 'Las existencias son obligatorias.',
            'existencias.integer' => 'Las existencias deben ser un número entero.',
            'existencias.min' => 'Las existencias no pueden ser menores que cero.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser Disponible o Agotado.',
        ];
    }
}