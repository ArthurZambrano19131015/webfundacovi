<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Acceso Denegado</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-[#F3EAC0] h-screen flex flex-col items-center justify-center font-sans px-6 text-center relative overflow-hidden">
    
    <div class="text-9xl mb-4">⛔</div>
    <h1 class="text-6xl font-black text-red-600 drop-shadow-sm mb-2">403</h1>
    <h2 class="text-2xl font-bold text-gray-800 mb-6 uppercase tracking-widest">Acceso Restringido</h2>
    <p class="text-gray-600 max-w-md mx-auto mb-8 font-medium leading-relaxed">
        {{ $exception->getMessage() ?: 'No tienes los permisos necesarios (Rol de Administrador) para entrar a esta zona del sistema.' }}
    </p>

    <button onclick="window.history.back()" class="bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-8 rounded-full shadow-lg transition transform hover:-translate-y-1">
        &larr; Regresar a zona segura
    </button>

</body>
</html>