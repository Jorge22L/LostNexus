<section class="flex items-center justify-center min-h-screen bg-gray-100 py-8">
    <?php if (!empty($alertas['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php foreach ($alertas['error'] as $error): ?>
                <p><?php echo $error ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="w-full max-w-4xl bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Encabezado con color -->
        <div class="bg-gradient-to-r from-cyan-500 to-teal-400 p-6">
            <h2 class="text-2xl font-bold text-white">Devolver Objeto Perdido</h2>
            <p class="text-white/90">Registra la devolución del objeto a su dueño</p>
        </div>

        <!-- Información del objeto -->
        <div class="p-6 bg-gray-50 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-gray-700 font-medium">Objeto:</p>
                    <p class="text-gray-900"><?php echo $objeto->nombre; ?></p>
                </div>
                <div>
                    <p class="text-gray-700 font-medium">Categoría:</p>
                    <p class="text-gray-900"><?php echo $objeto->categoria()->nombre; ?></p>
                </div>
                <div>
                    <p class="text-gray-700 font-medium">Reportado el:</p>
                    <p class="text-gray-900"><?php echo $objeto->fecha_reporte; ?></p>
                </div>
            </div>
        </div>

        <form class="p-6 space-y-6" method="POST" action="/objetosperdidos/devolver/<?php echo $objeto->id; ?>" enctype="multipart/form-data">

            <?php echo $router->csrfField(); ?>

            <!-- Mostrar error CSRF si existe -->
            <?php if (isset($_SESSION['csrf_error'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['csrf_error']; ?>
                    <?php unset($_SESSION['csrf_error']); ?>
                </div>
            <?php endif; ?>

            <!-- Grupo de Campos -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Columna Izquierda - Datos del Reclamante -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Datos del Reclamante</h3>

                    <!-- Nombre -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="nombre">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition capitalize"
                            type="text" name="nombre" id="nombre" required>
                    </div>

                    <!-- Apellido -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="apellido">
                            Apellido <span class="text-red-500">*</span>
                        </label>
                        <input class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition capitalize"
                            type="text" name="apellido" id="apellido" required>
                    </div>

                    <!-- Cédula -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="cedula">
                            Cédula <span class="text-red-500">*</span>
                        </label>
                        <input class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition uppercase"
                            type="text" name="cedula" id="cedula" required>
                    </div>

                    <!-- Carnet de Estudiante -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="carnet_estudiante">
                            Carnet de Estudiante
                        </label>
                        <input class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition uppercase"
                            type="text" name="carnet_estudiante" id="carnet_estudiante">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="email">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition lowercase"
                            type="email" name="email" id="email" required>
                    </div>
                </div>

                <!-- Columna Derecha - Detalles de Devolución -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Detalles de Devolución</h3>

                    <!-- Observaciones -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="observaciones">
                            Observaciones <span class="text-red-500">*</span>
                        </label>
                        <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition min-h-[120px]"
                            name="observaciones" id="observaciones" required
                            placeholder="Describe cómo verificaste que el reclamante es el dueño legítimo"></textarea>
                    </div>

                    <!-- Evidencia -->
                    <div>
                        <label for="evidencia" class="block text-gray-700 font-medium mb-1">Evidencia <span class="text-red-500">*</span></label>
                        <!-- Dropzone -->
                        <div id="dropzone" class="flex items-center justify-center w-full border-2 border-dashed border-gray-300 rounded-lg transition-all duration-200 hover:border-cyan-500">
                            <label class="flex flex-col w-full cursor-pointer">
                                <div class="flex flex-col items-center justify-center py-8 px-4 text-center">
                                    <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium text-cyan-600">Click para subir</span> foto de evidencia
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">Formatos: PNG, JPG (Max. 5MB)</p>
                                </div>
                                <input id="evidencia" name="evidencia" type="file" class="hidden" accept="image/jpeg, image/png, image/webp" required>
                            </label>
                        </div>

                        <!-- Preview (oculto inicialmente) -->
                        <div id="preview" class="mt-2 hidden relative bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                            <img id="previewImage" class="w-full h-48 object-contain">
                            <button type="button" id="removeImage" class="absolute top-2 right-2 bg-white/80 hover:bg-white text-red-500 rounded-full w-8 h-8 flex items-center justify-center backdrop-blur-sm transition-all shadow-md hover:scale-105 hover:text-red-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Campos ocultos -->
                    <input type="hidden" name="usuario_atiende" value="<?php echo $_SESSION['usuario_id'] ?? ''; ?>">
                    <input type="hidden" name="idobjeto" value="<?php echo $objeto->id; ?>">
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex flex-col md:flex-row gap-4 pt-4 border-t border-gray-200">
                <a href="/objetosperdidos/ver/<?php echo $objeto->id; ?>"
                    class="flex-1 text-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-4 rounded-lg shadow hover:shadow-md transition duration-300">
                    Cancelar
                </a>

                <button type="submit"
                    class="flex-1 bg-gradient-to-r from-green-500 to-teal-500 hover:from-green-600 hover:to-teal-600 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition duration-300 ease-in-out transform cursor-pointer hover:-translate-y-1">
                    Confirmar Devolución
                </button>
            </div>
        </form>
    </div>
</section>