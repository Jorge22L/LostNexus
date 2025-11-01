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
            <h2 class="text-2xl font-bold text-white">Editar Categoría</h2>
            <p class="text-white/90">Actualiza los datos de la categoría</p>
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
            <!-- Campo ID (oculto) -->
            <?php echo $router->csrfField(); ?>

            <!-- Mostrar error CSRF si existe -->
            <?php if (isset($_SESSION['csrf_error'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['csrf_error']; ?>
                    <?php unset($_SESSION['csrf_error']); ?>
                </div>
            <?php endif; ?>

            <input type="hidden" name="id" value="<?php echo $categorias->id; ?>">

            <!-- Campo Nombre -->
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="nombre">
                    Nombre de la Categoría <span class="text-red-500">*</span>
                </label>
                <input class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition capitalize"
                    type="text" name="nombre" id="nombre" required
                    value="<?php echo htmlspecialchars($categorias->nombre); ?>"
                    placeholder="Ej: Electrónicos, Documentos, Ropa...">
            </div>

            <!-- Botones de Acción -->
            <div class="pt-4 flex gap-4">
                <a href="/categorias" class="w-1/3 flex items-center justify-center bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition duration-300 ease-in-out">
                    Cancelar
                </a>
                <button class="w-2/3 bg-gradient-to-r from-cyan-500 to-teal-500 hover:from-cyan-600 hover:to-teal-600 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition duration-300 ease-in-out transform cursor-pointer hover:-translate-y-1"
                    type="submit">
                    Actualizar Categoría
                </button>
            </div>
        </form>
    </div>
</section>