<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> UTT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Si estás usando Bootstrap, mantén tus estilos, aquí adaptamos los enlaces directos -->
</head>
<body class="bg-gray-100">

    <!-- Barra de Navegación Global -->
    <nav class="bg-gray-800 text-white shadow-md mb-6">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a class="font-bold text-xl" href="/categorias">Sistema UTT</a>
            <div class="space-x-4">
                <a class="{{ request()->is('categorias*') ? 'text-blue-400 underline' : 'hover:text-gray-300' }}" href="/categorias">Categorías</a>
                <a class="{{ request()->is('productos*') ? 'text-blue-400 underline' : 'hover:text-gray-300' }}" href="/productos">Productos</a>
                <a class="{{ request()->is('clientes*') ? 'text-blue-400 underline' : 'hover:text-gray-300' }}" href="/clientes">Clientes</a>
                <a class="{{ request()->is('ventas*') ? 'text-blue-400 underline' : 'hover:text-gray-300' }}" href="/ventas">Ventas</a>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md text-center">
        <h1 class="text-3xl font-bold mb-4">Bienvenido al Sistema UTT</h1>
        <p class="text-gray-600 mb-6">Selecciona un módulo en la barra de navegación superior para comenzar a administrar el sistema.</p>
        <a href="/categorias" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Ir a Categorías</a>
    </div>

</body>
</html>