<div class="bg-(--color-bg)/10 py-6">
    <div class="container mx-auto px-4">
        <h2 class="text-(--color-secondary) font-bold text-2xl text-center">Objetos Perdidos</h2>
    </div>
</div>

<section class="bg-(--color-bg)/10 py-12">
    <div class="container mx-auto px-4">
        <form class="mb-8" method="GET" action="/objetosperdidos">
            <!-- Filtros -->
            <div class="mb-8">
                <!-- Búsqueda con icono -->
                <div class="relative w-full mb-4">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" placeholder="Buscar objeto..."
                        class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 bg-gray-200 focus:outline-none focus:ring-2 focus:ring-(--color-bg) focus:border-transparent"
                        name="nombre" value="<?php echo htmlspecialchars($nombre_filtro ?? ''); ?>">
                </div>

                <div class="flex flex-col sm:flex-row gap-3 w-full">
                    <!-- Categoría con icono -->
                    <div class="flex-1 min-w-0 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                            </svg>
                        </div>
                        <select class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 bg-gray-200 appearance-none focus:outline-none focus:ring-2 focus:ring-(--color-bg) focus:border-transparent" name="categoria">
                            <option value="">Todas las categorías</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?php echo $categoria->id; ?>" <?php echo ($categoria_filtro ?? '') == $categoria->id ? 'selected' : ''; ?>> <?php echo htmlspecialchars($categoria->nombre); ?> </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="flex-1 min-w-0 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <input type="date"
                            class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 bg-gray-200 focus:outline-none focus:ring-2 focus:ring-(--color-bg) focus:border-transparent"
                            placeholder="Filtrar por fecha" name="fecha" value="<?php echo htmlspecialchars($fecha_filtro ?? ''); ?>">
                    </div>

                    <!-- Botón con icono -->
                    <button type="submit" class="flex-1 sm:flex-none px-4 py-2 bg-(--color-secondary) text-white rounded-lg hover:bg-(--color-secondary) transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        <span>Filtrar</span>
                    </button>
                </div>
            </div>
        </form>

        <!-- Mensaje cuando no hay resultados -->
        <?php if (empty($objetos)): ?>
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No se encontraron objetos</h3>
                <p class="mt-2 text-gray-600">
                    <?php echo !empty($nombre_filtro) || !empty($categoria_filtro) || !empty($fecha_filtro) ? 
                        'No hay objetos que coincidan con tus criterios de búsqueda.' : 
                        'Actualmente no hay objetos perdidos registrados.' ?>
                </p>
                <?php if (!empty($nombre_filtro) || !empty($categoria_filtro) || !empty($fecha_filtro)): ?>
                    <div class="mt-6">
                        <a href="/objetosperdidos" class="inline-flex items-center px-4 py-2 bg-(--color-bg) border border-transparent rounded-md font-semibold text-white hover:bg-(--color-secondary) focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-(--color-bg) transition-colors">
                            Limpiar filtros
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Listado de objetos -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Card de Objeto -->
                <?php foreach ($objetos as $objeto): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <div class="h-48 overflow-hidden bg-gray-100 flex items-center justify-center">
                            <img class="h-full w-full object-contain hover:scale-105 transition-all duration-500" src="/imagenes/<?php echo $objeto->foto; ?>" alt="<?php echo $objeto->nombre; ?>">
                        </div>
                        <div class="p-4">
                            <div class="flex flex-row justify-between items-start mb-2">
                                <div>
                                    <h3 class="font-bold text-lg text-(--color-accent)"><?php echo htmlspecialchars($objeto->nombre); ?></h3>
                                    <p class="text-xs text-(--color-accent)">ID: <?php echo htmlspecialchars($objeto->id); ?></p>
                                </div>
                                <span class="font-semibold text-xs text-(--color-primary)"><?php echo htmlspecialchars($objeto->fecha_reporte); ?></span>
                            </div>
                            <div class="flex justify-between items-center mt-4">
                                <a href="/objetosperdidos/ver/<?php echo $objeto->id; ?>" class="bg-(--color-primary) text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors hover:bg-(--color-primary)/80">Ver detalles</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Paginación -->
            <?php if ($totalPaginas > 1): ?>
                <div class="flex items-center justify-center mt-8 space-x-2">
                    <!-- Anterior -->
                    <a href="/objetosperdidos?page=<?php echo max(1, $paginaActual - 1); ?>&nombre=<?php echo urlencode($nombre_filtro ?? ''); ?>&categoria=<?php echo urlencode($categoria_filtro ?? ''); ?>&fecha=<?php echo urlencode($fecha_filtro ?? ''); ?>"
                        class="px-4 py-2 border rounded-lg flex items-center <?php echo $paginaActual == 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-cyan-600 hover:bg-cyan-50 border-cyan-300'; ?>">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Anterior
                    </a>

                    <!-- Números -->
                    <?php
                    $inicio = max(1, $paginaActual - 2);
                    $fin = min($totalPaginas, $paginaActual + 2);

                    if ($inicio > 1) echo '<span class="px-3 py-2">...</span>';

                    for ($i = $inicio; $i <= $fin; $i++): ?>
                        <a href="/objetosperdidos?page=<?php echo $i; ?>&nombre=<?php echo urlencode($nombre_filtro ?? ''); ?>&categoria=<?php echo urlencode($categoria_filtro ?? ''); ?>&fecha=<?php echo urlencode($fecha_filtro ?? ''); ?>"
                            class="px-4 py-2 border rounded-lg <?php echo $i == $paginaActual ? 'bg-cyan-500 text-white border-cyan-500' : 'bg-white text-gray-700 hover:bg-gray-50 border-gray-300'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor;

                    if ($fin < $totalPaginas) echo '<span class="px-3 py-2">...</span>';
                    ?>

                    <!-- Siguiente -->
                    <a href="/objetosperdidos?page=<?php echo min($totalPaginas, $paginaActual + 1); ?>&nombre=<?php echo urlencode($nombre_filtro ?? ''); ?>&categoria=<?php echo urlencode($categoria_filtro ?? ''); ?>&fecha=<?php echo urlencode($fecha_filtro ?? ''); ?>"
                        class="px-4 py-2 border rounded-lg flex items-center <?php echo $paginaActual == $totalPaginas ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-cyan-600 hover:bg-cyan-50 border-cyan-300'; ?>">
                        Siguiente
                        <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>