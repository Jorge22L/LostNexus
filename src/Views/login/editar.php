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
            <h2 class="text-2xl font-bold text-white">Editar Usuario</h2>
            <p class="text-white/90">Actualiza los datos del usuario</p>
        </div>

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
            <input type="hidden" name="id" value="<?php echo $usuario->id; ?>">

            <!-- Campo Nombre -->
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="nombre">
                    Nombre <span class="text-red-500">*</span>
                </label>
                <input class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition capitalize"
                    type="text" name="nombre" id="nombre" required
                    value="<?php echo htmlspecialchars($usuario->nombre); ?>"
                    placeholder="Ej: Juan">
            </div>

            <!-- Campo Apellido -->
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="apellido">
                    Apellido <span class="text-red-500">*</span>
                </label>
                <input class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition capitalize"
                    type="text" name="apellido" id="apellido" required
                    value="<?php echo htmlspecialchars($usuario->apellido); ?>"
                    placeholder="Ej: Perez">
            </div>

            <!-- Campo Usuario -->
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="nombre_usuario">
                    Usuario <span class="text-red-500">*</span>
                </label>
                <input class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition"
                    type="text" name="nombre_usuario" id="nombre_usuario" required
                    value="<?php echo htmlspecialchars($usuario->nombre_usuario); ?>"
                    placeholder="Ej: juanperez">
            </div>
            <!-- Campo Contraseña -->
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="pwd">
                    Contraseña <span class="text-red-500">*</span>
                </label>
                <input class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition"
                    type="password" name="pwd" id="pwd" required
                    placeholder="Ej: ********">
            </div>

            <!-- Botones de Acción -->
            <div class="pt-4 flex gap-4">
                <a href="/admin/usuarios" class="w-1/3 flex items-center justify-center bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition duration-300 ease-in-out">
                    Cancelar
                </a>
                <button class="w-2/3 bg-gradient-to-r from-cyan-500 to-teal-500 hover:from-cyan-600 hover:to-teal-600 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition duration-300 ease-in-out transform cursor-pointer hover:-translate-y-1"
                    type="submit">
                    Actualizar Usuario
                </button>
            </div>
        </form>
    </div>
</section>