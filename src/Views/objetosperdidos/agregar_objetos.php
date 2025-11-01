<section class="flex items-center justify-center min-h-screen bg-gray-100 py-8">
    <?php if (!empty($alertas['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php foreach ($alertas['error'] as $error): ?>
                <p><?php echo $error ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <div class="w-full max-w-2xl bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Encabezado con color -->
        <div class="bg-gradient-to-r from-cyan-500 to-teal-400 p-6">
            <h2 class="text-2xl font-bold text-white">Agregar Objeto Perdido</h2>
            <p class="text-white/90">Por favor completa los detalles del objeto encontrado</p>
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

        <form class="p-6 space-y-6" action="/agregar_objetos/crear" method="post" enctype="multipart/form-data">

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
                <!-- Columna Izquierda -->
                <div class="space-y-4">
                    <!-- Campo Objeto -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="nombre">
                            Nombre del Objeto <span class="text-red-500">*</span>
                        </label>
                        <input class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition"
                            type="text" name="nombre" id="nombre" required
                            placeholder="Ej: Billetera, Teléfono, Llaves...">
                    </div>

                    <!-- Campo Punto de Recepción -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="idpunto_recepcion">
                            Punto de recepcion <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition"
                            name="idpunto_recepcion" id="idpunto_recepcion" required>
                            <option value="" disabled selected>Seleccione un punto de recepcion</option>
                            <?php foreach ($puntosRecepcion as $puntoRecepcion): ?>
                                <option value="<?php echo $puntoRecepcion->id; ?>"><?php echo $puntoRecepcion->nombre; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Campo Foto -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Imagen del Objeto</label>

                        <!-- Dropzone (visible inicialmente) -->
                        <div id="dropzone" class="flex items-center justify-center w-full border-2 border-dashed border-gray-300 rounded-lg transition-all duration-200 hover:border-cyan-500">
                            <label class="flex flex-col w-full cursor-pointer">
                                <div class="flex flex-col items-center justify-center py-8 px-4 text-center">
                                    <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium text-cyan-600">Click para subir</span> o arrastra aquí
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">Formatos: PNG, JPG (Max. 5MB)</p>
                                </div>
                                <input id="foto" name="foto" type="file" class="hidden" accept="image/jpeg, image/png, image/webp">
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
                </div>

                <!-- Columna Derecha -->
                <div class="space-y-4">
                    <!-- Campo Descripción -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="descripcion">
                            Descripción Detallada <span class="text-red-500">*</span>
                        </label>
                        <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition min-h-[120px]"
                            name="descripcion" id="descripcion" required
                            placeholder="Color, marca, características distintivas..."></textarea>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="descripcion">
                            Seleccione el tipo de Objeto<span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition"
                            name="idcategoria" id="idcategoria" required>
                            <option value="" disabled selected>Seleccione un tipo de objeto</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?php echo $categoria->id; ?>"><?php echo $categoria->nombre; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Campo Observaciones -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="observaciones">
                            Información Adicional
                        </label>
                        <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition min-h-[120px]"
                            name="observaciones" id="observaciones"
                            placeholder="Fecha aproximada, hora, persona que lo encontró..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Botón de Envío -->
            <div class="pt-4">
                <button class="w-full bg-gradient-to-r from-cyan-500 to-teal-500 hover:from-cyan-600 hover:to-teal-600 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition duration-300 ease-in-out transform cursor-pointer hover:-translate-y-1"
                    type="submit">
                    Agregar Objeto
                </button>
            </div>
        </form>
    </div>
</section>