<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo de Ventas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <nav class="bg-gray-800 text-white shadow-md mb-6">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a class="font-bold text-xl" href="/">Sistema UTT</a>
            <div class="space-x-4">
                <a class="{{ request()->is('categorias*') ? 'text-blue-400 underline font-bold' : 'hover:text-gray-300' }}" href="/categorias">Categorías</a>
                <a class="{{ request()->is('productos*') ? 'text-blue-400 underline font-bold' : 'hover:text-gray-300' }}" href="/productos">Productos</a>
                <a class="{{ request()->is('clientes*') ? 'text-blue-400 underline font-bold' : 'hover:text-gray-300' }}" href="/clientes">Clientes</a>
                <a class="{{ request()->is('ventas*') ? 'text-blue-400 underline font-bold' : 'hover:text-gray-300' }}" href="/ventas">Ventas</a>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-4">Gestión de Ventas</h1>

        @if (session('mensaje'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">¡Éxito!</strong>
                <span class="block sm:inline">{{ session('mensaje') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">¡Atención!</strong>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ isset($venta) ? route('ventas.update', $venta->id) : '/ventas' }}" method="POST" class="mb-6">
            @csrf
            @if(isset($venta))
                @method('PUT')
            @endif

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Cliente:</label>
                <select name="cliente_id" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">Seleccione un cliente</option>
                    @foreach($clientes as $cli)
                        <option value="{{ $cli->id }}" {{ (isset($venta) && $venta->cliente_id == $cli->id) ? 'selected' : '' }}>
                            {{ $cli->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Total:</label>
                <input type="number" step="0.01" name="total" value="{{ old('total', $venta->total ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                {{ isset($venta) ? 'Actualizar Venta' : 'Guardar Venta' }}
            </button>
        </form>

        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">ID</th>
                    <th class="py-2 px-4 border-b">Cliente</th>
                    <th class="py-2 px-4 border-b">Total</th>
                    <th class="py-2 px-4 border-b">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ventas as $ven)
                <tr>
                    <td class="py-2 px-4 border-b text-center">{{ $ven->id }}</td>
                    <td class="py-2 px-4 border-b text-center">{{ $ven->cliente->nombre ?? 'Sin cliente' }}</td>
                    <td class="py-2 px-4 border-b text-center">${{ $ven->total }}</td>
                    <td class="py-2 px-4 border-b text-center">
                        <a href="{{ route('ventas.edit', $ven->id) }}" class="text-yellow-600 hover:underline mr-2">Editar</a>
                        <form action="{{ route('ventas.delete', $ven->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>