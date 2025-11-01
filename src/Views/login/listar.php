<div class="bg-(--color-bg)/10 py-6">
    <div class="container mx-auto px-4">
        <h2 class="text-(--color-secondary) font-bold text-2xl text-center mb-2">Listado de Usuarios</h2>
        <p class="text-center text-(--color-secondary)/80 max-w-2xl mx-auto">Administra los usuarios disponibles para clasificar los objetos perdidos</p>
    </div>
</div>

<section class="bg-white py-8 px-4 sm:px-6 lg:px-8">
    <div class="container mx-auto">
        <!-- Barra de herramientas con búsqueda -->
        <form method="get" action="/admin/usuarios" class="flex justify-between items-center mb-6">
            <div class="relative w-64">
                <input type="text" name="busqueda" value="<?php echo htmlspecialchars($busqueda ?? ''); ?>"
                    placeholder="Buscar por nombre, apellido o usuario..."
                    class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-(--color-bg) focus:border-transparent">
                <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <a href="/admin/usuarios/crear" class="bg-(--color-bg) hover:bg-(--color-secondary) text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Agregar Usuario
            </a>
        </form>

        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
            <?php if (empty($usuarios)): ?>
                <div class="p-8 text-center">
                    <p class="text-gray-500">No se encontraron usuarios</p>
                    <?php if (!empty($busqueda)): ?>
                        <a href="/admin/usuarios" class="text-(--color-bg) hover:underline mt-2 inline-block">Mostrar todos</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-(--color-bg)/10">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-(--color-secondary) uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-(--color-secondary) uppercase tracking-wider">Nombre</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-(--color-secondary) uppercase tracking-wider">Apellido</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-(--color-secondary) uppercase tracking-wider">Usuario</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-(--color-secondary) uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $usuario->id; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-md text-gray-700"><?php echo htmlspecialchars($usuario->nombre); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-md text-gray-700"><?php echo htmlspecialchars($usuario->apellido); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-md text-gray-700"><?php echo htmlspecialchars($usuario->nombre_usuario); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-md text-gray-700">
                                    <div class="flex space-x-3">
                                        <a href="/admin/usuarios/editar/<?php echo $usuario->id; ?>" class="text-(--color-bg) hover:text-(--color-secondary) p-1 rounded hover:bg-(--color-bg)/10 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>
                                        <button onclick="this.nextElementSibling.showModal()"
                                            class="text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-500/10 transition-colors cursor-pointer">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>

                                        <dialog class="fixed top-0 left-0 w-full h-full z-50 flex items-center justify-center p-4 bg-transparent bg-opacity-50 backdrop:bg-gray-600/50 opacity-0 invisible transition-opacity duration-300 open:opacity-100 open:visible">
                                            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full mx-auto transform transition-all">
                                                <div class="p-6">
                                                    <div class="flex justify-between items-start">
                                                        <h3 class="text-lg font-bold text-(--color-secondary)">Confirmar eliminación</h3>
                                                        <form method="dialog">
                                                            <button class="text-gray-400 hover:text-gray-500">
                                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>

                                                    <div class="mt-4">
                                                        <p class="text-(--color-secondary)/80">¿Estás seguro de eliminar el usuario "<?php echo htmlspecialchars($usuario->nombre); ?>"?</p>
                                                    </div>

                                                    <div class="mt-6 flex justify-end space-x-3">
                                                        <form method="dialog">
                                                            <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                                                                Cancelar
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="/admin/usuarios/eliminar">

                                                            <?php echo $router->csrfField(); ?>

                                                            <!-- Mostrar error CSRF si existe -->
                                                            <?php if (isset($_SESSION['csrf_error'])): ?>
                                                                <div class="alert alert-danger">
                                                                    <?php echo $_SESSION['csrf_error']; ?>
                                                                    <?php unset($_SESSION['csrf_error']); ?>
                                                                </div>
                                                            <?php endif; ?>

                                                            <input type="hidden" name="id" value="<?php echo $usuario->id; ?>">
                                                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                                                Eliminar
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </dialog>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Paginación -->
                <?php if ($totalPaginas > 1): ?>
                    <div class="bg-white px-6 py-3 flex items-center justify-between border-t border-gray-200">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <a href="/admin/usuarios?page=<?php echo max(1, $paginaActual - 1); ?>&busqueda=<?php echo urlencode($busqueda); ?>"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 <?php echo $paginaActual == 1 ? 'opacity-50 cursor-not-allowed' : ''; ?>">
                                Anterior
                            </a>
                            <a href="/admin/usuarios?page=<?php echo min($totalPaginas, $paginaActual + 1); ?>&busqueda=<?php echo urlencode($busqueda); ?>"
                                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 <?php echo $paginaActual == $totalPaginas ? 'opacity-50 cursor-not-allowed' : ''; ?>">
                                Siguiente
                            </a>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Mostrando <span class="font-medium"><?php echo $inicio; ?></span> a
                                    <span class="font-medium"><?php echo $fin; ?></span> de
                                    <span class="font-medium"><?php echo $totalUsuarios; ?></span> resultados
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <!-- Botón Anterior -->
                                    <a href="/admin/usuarios?page=<?php echo max(1, $paginaActual - 1); ?>&busqueda=<?php echo urlencode($busqueda); ?>"
                                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 <?php echo $paginaActual == 1 ? 'opacity-50 cursor-not-allowed' : ''; ?>">
                                        <span class="sr-only">Anterior</span>
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </a>

                                    <!-- Números de página -->
                                    <?php
                                    $paginaInicio = max(1, $paginaActual - 2);
                                    $paginaFin = min($totalPaginas, $paginaActual + 2);

                                    if ($paginaInicio > 1): ?>
                                        <a href="/admin/usuarios?page=1&busqueda=<?php echo urlencode($busqueda); ?>"
                                            class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            1
                                        </a>
                                        <?php if ($paginaInicio > 2): ?>
                                            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                                ...
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php for ($i = $paginaInicio; $i <= $paginaFin; $i++): ?>
                                        <a href="/admin/usuarios?page=<?php echo $i; ?>&busqueda=<?php echo urlencode($busqueda); ?>"
                                            class="<?php echo $i == $paginaActual ? 'z-10 bg-(--color-bg)/10 border-(--color-bg) text-(--color-secondary)' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'; ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>

                                    <?php if ($paginaFin < $totalPaginas): ?>
                                        <?php if ($paginaFin < $totalPaginas - 1): ?>
                                            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                                ...
                                            </span>
                                        <?php endif; ?>
                                        <a href="/admin/usuarios?page=<?php echo $totalPaginas; ?>&busqueda=<?php echo urlencode($busqueda); ?>"
                                            class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            <?php echo $totalPaginas; ?>
                                        </a>
                                    <?php endif; ?>

                                    <!-- Botón Siguiente -->
                                    <a href="/admin/usuarios?page=<?php echo min($totalPaginas, $paginaActual + 1); ?>&busqueda=<?php echo urlencode($busqueda); ?>"
                                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 <?php echo $paginaActual == $totalPaginas ? 'opacity-50 cursor-not-allowed' : ''; ?>">
                                        <span class="sr-only">Siguiente</span>
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                </nav>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</section>