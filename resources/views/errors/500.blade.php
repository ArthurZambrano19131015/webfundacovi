<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - Error del Servidor</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 h-screen flex flex-col items-center justify-center font-sans px-6 text-center">
    
    <div class="text-9xl mb-4">🔥</div>
    <h1 class="text-6xl font-black text-gray-800 drop-shadow-sm mb-2">500</h1>
    <h2 class="text-2xl font-bold text-orange-600 mb-6 uppercase tracking-widest">Error Crítico</h2>
    <p class="text-gray-600 max-w-md mx-auto mb-8 font-medium leading-relaxed">
        El servidor ha experimentado un problema inesperado o la base de datos no está respondiendo. Intenta recargar la página.
    </p>

    <div class="flex gap-4">
        <button onclick="window.location.reload()" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-full shadow-lg transition transform hover:-translate-y-1">
            ↻ Recargar Página
        </button>
        <button onclick="window.history.back()" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-3 px-8 rounded-full shadow-lg transition transform hover:-translate-y-1">
            Volver Atrás
        </button>
    </div>

</body>
</html>