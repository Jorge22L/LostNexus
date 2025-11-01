<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">Información del objeto devuelto</h1>
            <div class="w-20 h-1 bg-green-500 mx-auto"></div>
        </div>

        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md overflow-hidden">
            <div class="md:flex">
                <div class="md:w-1/2 bg-gray-100 flex flex-col items-center justify-center p-6">
                    <!-- Imagen del objeto -->
                    <img class="h-64 w-full object-contain mb-4" src="/imagenes/<?php echo $objeto->foto; ?>" alt="<?php echo $objeto->nombre; ?>">
                    
                    <!-- Evidencia de devolución -->
                    <?php if(isset($objeto->reclamacion) && !empty($objeto->reclamacion->evidencia)): ?>
                    <div class="w-full mt-4">
                        <h3 class="font-semibold text-gray-800 mb-2">Evidencia de devolución</h3>
                        <img class="h-64 w-full object-contain border border-gray-300" src="/evidencia/<?php echo $objeto->reclamacion->evidencia; ?>" alt="Evidencia de devolución">
                        <p class="text-sm text-gray-500 mt-2">Fecha de devolución: <?php echo $objeto->fecha_devolucion; ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="md:w-1/2 p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2"><?php echo $objeto->nombre; ?></h2>

                    <!-- Categoría -->
                    <p class="text-gray-600 mb-4">
                        <span class="font-medium">Categoría:</span>
                        <?php echo $objeto->categoria->nombre ?? 'No especificada'; ?>
                    </p>

                    <!-- Fechas importantes -->
                    <div class="mb-4">
                        <p class="text-gray-600">
                            <span class="font-medium">Reportado el:</span>
                            <?php echo $objeto->fecha_reporte; ?>
                        </p>
                        <p class="text-gray-600">
                            <span class="font-medium">Devuelto el:</span>
                            <?php echo $objeto->fecha_devolucion; ?>
                        </p>
                    </div>

                    <!-- Ubicación -->
                    <div class="flex items-center mb-6">
                        <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-gray-600">
                            <?php echo $objeto->puntoRecepcion->nombre ?? 'Desconocido'; ?>
                            (<?php echo $objeto->puntoRecepcion->ubicacion ?? 'Ubicación no especificada'; ?>)
                        </span>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-800 mb-2">Descripción</h3>
                        <p class="text-gray-600"><?php echo $objeto->descripcion; ?></p>
                    </div>

                    <!-- Información del reclamante (si existe) -->
                    <?php if(isset($objeto->reclamante)): ?>
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 mb-6">
                        <h3 class="font-semibold text-gray-800 mb-2">Información del reclamante</h3>
                        <p class="text-gray-600 mb-1">
                            <span class="font-medium">Nombre:</span> 
                            <?php echo $objeto->reclamante->nombre . ' ' . $objeto->reclamante->apellido; ?>
                        </p>
                        <p class="text-gray-600 mb-1">
                            <span class="font-medium">Cédula:</span> 
                            <?php echo $objeto->reclamante->cedula; ?>
                        </p>
                        <?php if(!empty($objeto->reclamante->carnet_estudiante)): ?>
                        <p class="text-gray-600">
                            <span class="font-medium">Carnet de estudiante:</span> 
                            <?php echo $objeto->reclamante->carnet_estudiante; ?>
                        </p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Observaciones -->
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-300">
                        <h3 class="font-semibold text-gray-800 mb-2">Observaciones</h3>
                        <p class="text-gray-600">
                            <?php echo $objeto->observaciones; ?>
                            <?php if(isset($objeto->reclamacion) && !empty($objeto->reclamacion->observaciones)): ?>
                            <br><br>
                            <span class="font-medium">Observaciones de devolución:</span>
                            <?php echo $objeto->reclamacion->observaciones; ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between gap-4">
                    <div class="flex justify-center sm:justify-start">
                        <a href="/objetosdevueltos" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver al listado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>