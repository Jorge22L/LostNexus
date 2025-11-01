<div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 p-4">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
        <!-- Icono de error -->
        <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-100 mb-6">
            <svg class="h-16 w-16 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        
        <!-- Título y mensaje -->
        <h1 class="text-4xl font-bold text-gray-800 mb-4">404</h1>
        <h2 class="text-2xl font-semibold text-gray-700 mb-4"><?= htmlspecialchars($mensaje ?? 'Página no encontrada') ?></h2>
        <p class="text-gray-600 mb-8">Lo sentimos, la página que estás buscando no existe o ha sido movida.</p>
        
        <!-- Botón principal dinámico -->
        <a href="<?= htmlspecialchars($redireccion ?? '/') ?>" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
            <?= $redireccion === '/login' ? 'Ir a Login' : 'Volver a Objetos Perdidos' ?>
        </a>
        
        <!-- Botón de volver atrás (siempre visible) -->
        <button onclick="window.history.back()" class="ml-4 text-blue-500 hover:text-blue-600 font-medium">
            Volver atrás
        </button>
    </div>
</div>