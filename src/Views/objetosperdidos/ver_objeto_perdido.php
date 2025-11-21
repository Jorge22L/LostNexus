<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <!-- Encabezado -->
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">Información del objeto</h1>
            <div class="w-20 h-1 bg-cyan-500 mx-auto"></div>
        </div>

        <!-- Tarjeta de detalle -->
        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md overflow-hidden">
            <!-- Imagen principal -->
            <div class="md:flex">
                <div class="md:w-1/2 bg-gray-100 flex items-center justify-center p-6">
                    <img class="h-96 w-full object-contain" src="/imagenes/<?php echo $objeto->foto; ?>" alt="<?php echo $objeto->nombre; ?>">
                </div>

                <!-- Detalles -->
                <div class="md:w-1/2 p-8">

                    <!-- Nombre -->
                    <h2 class="text-2xl font-bold text-gray-800 mb-2"><?php echo $objeto->nombre; ?></h2>

                    <!-- Categoría -->
                    <p class="text-gray-600 mb-4">
                        <span class="font-medium">Categoría:</span>
                        <?php echo $objeto->categoria()->nombre; ?>
                    </p>

                    <!-- Fecha -->
                    <p class="text-gray-600 mb-6">
                        <span class="font-medium">Reportado el:</span>
                        <?php echo $objeto->fecha_reporte; ?>
                    </p>

                    <!-- Ubicación -->
                    <div class="flex items-center mb-6">
                        <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-gray-600"><?php echo $objeto->puntoRecepcion()->nombre; ?></span>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-800 mb-2">Descripción</h3>
                        <p class="text-gray-600"><?php echo $objeto->descripcion; ?></p>
                    </div>

                    <!-- Observaciones -->
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-300">
                        <h3 class="font-semibold text-gray-800 mb-2">Observaciones</h3>
                        <p class="text-gray-600"><?php echo $objeto->observaciones; ?></p>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between gap-4">
                    <div class="flex justify-center sm:justify-start">
                        <a href="/objetosperdidos" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver al listado
                        </a>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 justify-center sm:justify-end">
                        <?php
                        if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
                            $esAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === 'admin';

                            // Calcular si el objeto tiene más de 2 meses
                            $fechaReporte = new DateTime($objeto->fecha_reporte);
                            $fechaLimite = (new DateTime())->modify('-2 months');
                            $esArchivado = $fechaReporte < $fechaLimite;

                            if ($esArchivado && $esAdmin) {
                                // Botón "Dar de baja" para admin en objetos archivados
                        ?>
                                <form method="POST" action="/objetosperdidos/dar-de-baja/<?php echo $objeto->id; ?>" onsubmit="return confirm('¿Estás seguro de que quieres dar de baja este objeto? Esta acción no se puede deshacer.');">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm cursor-pointer text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Dar de baja
                                    </button>
                                </form>
                        <?php
                            } else {
                                // Botones "Editar" y "Devolver" para objetos recientes
                                
                                // Editar: Solo si no es Estudiante NI Docente
                                if (!isset($_SESSION['rol_nombre']) || ($_SESSION['rol_nombre'] !== 'Estudiante' && $_SESSION['rol_nombre'] !== 'Docente')) {
                        ?>
                                <a href="/objetosperdidos/editar/<?php echo $objeto->id; ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-cyan-600 hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 transition-colors duration-200">
                                    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M21.28 6.4L11.74 15.94C10.79 16.89 7.97 17.33 7.34 16.7C6.71 16.07 7.14 13.25 8.09 12.3L17.64 2.75C17.88 2.49 18.16 2.29 18.48 2.14C18.8 2 19.14 1.92 19.49 1.91C19.84 1.91 20.18 1.97 20.51 2.1C20.83 2.23 21.12 2.42 21.37 2.67C21.61 2.92 21.81 3.21 21.94 3.54C22.07 3.86 22.13 4.21 22.12 4.55C22.11 4.9 22.03 5.25 21.89 5.56C21.74 5.88 21.54 6.17 21.28 6.4Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M11 4H6C4.94 4 3.92 4.42 3.17 5.17C2.42 5.92 2 6.94 2 8V18C2 19.06 2.42 20.08 3.17 20.83C3.92 21.58 4.94 22 6 22H17C19.21 22 20 20.2 20 18V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    Editar
                                </a>
                        <?php
                                }

                                // Devolver: Solo si no es Estudiante (Docente puede)
                                if ((!isset($_SESSION['rol_nombre']) || $_SESSION['rol_nombre'] !== 'Estudiante') && $objeto->estado !== 'devuelto') {
                        ?>
                                    <a href="/objetosperdidos/devolver/<?php echo $objeto->id; ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Devolver objeto
                                    </a>
                        <?php
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>

        </div>

    </div>

</section>