<section class="flex items-center justify-center min-h-screen bg-gray-100 py-8">
    <?php if (!empty($alertas['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 max-w-md mx-auto">
            <?php foreach ($alertas['error'] as $error): ?>
                <p><?php echo $error ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="w-full max-w-md bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Encabezado con color -->
        <div class="bg-gradient-to-r from-cyan-500 to-teal-400 p-6">
            <h2 class="text-2xl font-bold text-white">Editar Punto de Recepción</h2>
            <p class="text-white/90">Actualiza los datos del punto de recepción</p>
        </div>

        <!-- Alertas -->
        <?php if (!empty($alertas['error'])): ?>
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-md" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm">
                            <?php echo is_array($alertas['error']) ? implode('<br>', $alertas['error']) : $alertas['error']; ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($alertas['exito'])): ?>
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-md" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm">
                            <?php echo is_array($alertas['exito']) ? implode('<br>', $alertas['exito']) : $alertas['exito']; ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <!-- Fin Alertas -->

        <form class="p-6 space-y-6" method="post">

            <?php echo $router->csrfField(); ?>

            <!-- Mostrar error CSRF si existe -->
            <?php if (isset($_SESSION['csrf_error'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['csrf_error']; ?>
                    <?php unset($_SESSION['csrf_error']); ?>
                </div>
            <?php endif; ?>


            <!-- Campo ID (oculto) -->
            <input type="hidden" name="id" value="<?php echo $puntorecepcion->id; ?>">

            <!-- Campo Nombre -->
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="nombre">
                    Nombre del Punto <span class="text-red-500">*</span>
                </label>
                <input class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition capitalize"
                    type="text" name="nombre" id="nombre" required
                    value="<?php echo htmlspecialchars($puntorecepcion->nombre); ?>"
                    placeholder="Ej: Recepción, Parqueo, Estacionamiento">
            </div>

            <!-- Campo Ubicación -->
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="ubicacion">
                    Ubicación <span class="text-red-500">*</span>
                </label>
                <input class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition uppercase"
                    type="text" name="ubicacion" id="ubicacion" required
                    value="<?php echo htmlspecialchars($puntorecepcion->ubicacion); ?>"
                    placeholder="Ej: Edificio A, Edificio B, Edificio C">
            </div>

            <!-- Botones de Acción -->
            <div class="pt-4 flex flex-col sm:flex-row gap-4">
                <a href="/puntosrecepcion" class="flex-1 flex items-center justify-center gap-2 
              bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium 
              py-3 px-4 rounded-lg border border-gray-300
              shadow-sm hover:shadow-md transition-all duration-200 
              focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-opacity-50
              active:bg-gray-300 active:shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Cancelar
                </a>
                <button class="flex-1 flex items-center justify-center gap-2
                   bg-gradient-to-r from-cyan-500 to-teal-500 hover:from-cyan-600 hover:to-teal-600
                   text-white font-medium py-3 px-4 rounded-lg
                   shadow-md hover:shadow-lg transition-all duration-200
                   focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:ring-offset-2
                   active:from-cyan-700 active:to-teal-700 active:shadow-inner
                   disabled:opacity-70 disabled:cursor-not-allowed"
                    type="submit">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Actualizar Punto
                </button>
            </div>
        </form>
    </div>
</section>