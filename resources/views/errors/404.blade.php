<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Página no encontrada</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-[#F3EAC0] h-screen flex flex-col items-center justify-center font-sans px-6 text-center">
    
    <div class="text-9xl mb-4">🐝</div>
    <h1 class="text-6xl font-black text-gray-800 drop-shadow-sm mb-2">404</h1>
    <h2 class="text-2xl font-bold text-yellow-600 mb-6 uppercase tracking-widest">¡Te saliste del panal!</h2>
    <p class="text-gray-600 max-w-md mx-auto mb-8 font-medium leading-relaxed">
        La página que buscas no existe, ha sido movida o te has perdido explorando fuera del apiario. 
    </p>

    <div class="flex gap-4">
        <!-- Botón Volver Atrás (Regresa al historial anterior) -->
        <button onclick="window.history.back()" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-3 px-8 rounded-full shadow-lg transition transform hover:-translate-y-1">
            &larr; Volver Atrás
        </button>
        
        <!-- Botón Inicio -->
        <a href="/" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-8 rounded-full shadow-lg transition transform hover:-translate-y-1">
            Ir al Inicio
        </a>
    </div>

</body>
</html>