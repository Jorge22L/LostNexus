<section class="flex items-center justify-center min-h-screen bg-gray-100 py-8">
    <?php if (!empty($alertas['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 max-w-2xl w-full">
            <?php foreach ($alertas['error'] as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="w-full max-w-2xl bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Encabezado con color -->
        <div class="bg-gradient-to-r from-cyan-500 to-teal-400 p-6">
            <h2 class="text-2xl font-bold text-white">Editar Objeto Perdido</h2>
            <p class="text-white/90">Actualiza los detalles del objeto encontrado</p>
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

        <form method="POST" class="p-6 space-y-6" enctype="multipart/form-data">
            <?php echo $router->csrfField(); ?>

            <!-- Mostrar error CSRF si existe -->
            <?php if (isset($_SESSION['csrf_error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <?php echo $_SESSION['csrf_error']; ?>
                    <?php unset($_SESSION['csrf_error']); ?>
                </div>
            <?php endif; ?>

            <!-- Grupo de Campos -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Columna Izquierda -->
                <div class="space-y-4">
                    <!-- Campo Nombre -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="nombre">
                            Nombre del Objeto <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="nombre"
                            value="<?php echo htmlspecialchars($objeto->nombre); ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition capitalize"
                            required>
                    </div>

                    <!-- Campo Categoría -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="idcategoria">
                            Categoría <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition"
                            name="idcategoria" id="idcategoria" required>
                            <option value="" disabled>Seleccione un tipo de objeto</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?php echo $categoria->id; ?>" <?php echo $objeto->idcategoria === $categoria->id ? 'selected' : ''; ?>>
                                    <?php echo $categoria->nombre; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Campo Punto de Recepción -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="idpunto_recepcion">
                            Punto de recepción <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition"
                            name="idpunto_recepcion" id="idpunto_recepcion" required>
                            <option value="" disabled>Seleccione un punto de recepción</option>
                            <?php foreach ($puntosRecepcion as $puntoRecepcion): ?>
                                <option value="<?php echo $puntoRecepcion->id; ?>" <?php echo $objeto->idpunto_recepcion === $puntoRecepcion->id ? 'selected' : ''; ?>>
                                    <?php echo $puntoRecepcion->nombre; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Columna Derecha -->
                <div class="space-y-4">
                    <!-- Campo Descripción -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="descripcion">
                            Descripción Detallada <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            name="descripcion"
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition"
                            required><?php echo htmlspecialchars($objeto->descripcion); ?></textarea>
                    </div>

                    <!-- Campo Observaciones -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="observaciones">
                            Información Adicional
                        </label>
                        <textarea
                            name="observaciones"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition min-h-[120px]"><?php echo htmlspecialchars($objeto->observaciones); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Campo Foto -->
            <div>
                <label class="block text-gray-700 font-medium mb-1">Imagen del Objeto</label>

                <!-- Imagen actual -->
                <?php if (!empty($objeto->foto)): ?>
                    <div class="mb-4 flex flex-col items-center">
                        <div class="relative w-full max-w-xs">
                            <img class="h-48 w-full object-contain border rounded-lg" src="/imagenes/<?php echo htmlspecialchars($objeto->foto); ?>" alt="<?php echo htmlspecialchars($objeto->nombre); ?>">
                            <p class="text-sm text-gray-600 mt-2 text-center">Imagen actual</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bg-gray-100 p-4 rounded-lg text-center text-gray-500 mb-4">
                        No hay imagen disponible
                    </div>
                <?php endif; ?>

                <!-- Dropzone para nueva imagen -->
                <div class="flex items-center justify-center w-full">
                    <label class="flex flex-col w-full border-2 border-dashed border-gray-300 hover:border-cyan-500 rounded-lg cursor-pointer transition-colors duration-200">
                        <div class="flex flex-col items-center justify-center py-6 px-4">
                            <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-sm text-gray-500 text-center">
                                <span class="font-semibold text-cyan-600">Haz clic para subir</span> o arrastra una imagen
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Formatos: PNG, JPG o Webp (Max. 5MB)</p>
                        </div>
                        <input id="foto" name="foto" type="file" class="hidden" accept="image/jpeg, image/png, image/webp">
                    </label>
                </div>

                <!-- Preview de nueva imagen -->
                <div id="preview" class="mt-4 hidden relative max-w-xs mx-auto">
                    <img id="previewImage" class="h-48 w-full object-contain border rounded-lg">
                    <button type="button" id="removeImage" class="absolute top-2 right-2 bg-white/80 hover:bg-white text-red-500 rounded-full w-8 h-8 flex items-center justify-center backdrop-blur-sm transition-all shadow-md hover:scale-105 hover:text-red-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex flex-col-reverse sm:flex-row justify-between gap-4 pt-4">
                <a href="/objetosperdidos/ver/<?php echo $objeto->id; ?>"
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 text-center">
                    Cancelar
                </a>
                <button type="submit"
                    class="w-full sm:w-auto bg-gradient-to-r from-cyan-500 to-teal-500 hover:from-cyan-600 hover:to-teal-600 text-white font-bold py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition duration-300 ease-in-out transform cursor-pointer hover:-translate-y-1">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</section>