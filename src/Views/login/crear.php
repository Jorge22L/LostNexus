<section class="flex items-center justify-center min-h-screen bg-gray-100 py-8">

    <div class="w-full max-w-md bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Encabezado -->
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6">
            <h2 class="text-2xl font-bold text-white">Registrar nuevo usuario</h2>
            <p class="text-white/90">Crear una cuenta para administrar objetos perdidos.</p>
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

        <form class="p-6 space-y-4" method="POST">

            <?php echo $router->csrfField(); ?>

            <!-- Mostrar error CSRF si existe -->
            <?php if (isset($_SESSION['csrf_error'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['csrf_error']; ?>
                    <?php unset($_SESSION['csrf_error']); ?>
                </div>
            <?php endif; ?>

            <div>
                <label for="nombre" class="block text-gray-700 font-medium mb-1">Nombre <span class="text-red-500">*</span></label>
                <input class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300 ease-in-out capitalize" type="text" name="nombre" id="nombre" placeholder="Ej: Juan" required />
            </div>
            <div>
                <label for="apellido" class="block text-gray-700 font-medium mb-1">Apellido <span class="text-red-500">*</span></label>
                <input class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300 ease-in-out capitalize" type="text" name="apellido" id="apellido" placeholder="Ej: Perez" required />
            </div>
            <div>
                <label for="nombre_usuario" class="block text-gray-700 font-medium mb-1">Nombre de usuario <span class="text-red-500">*</span></label>
                <input class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300 ease-in-out" type="text" name="nombre_usuario" id="nombre_usuario" placeholder="Ej: juanperez" required />
            </div>
            <div>
                <label for="id_rol" class="block text-gray-700 font-medium mb-1">Rol <span class="text-red-500">*</span></label>
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300 ease-in-out" name="id_rol" id="id_rol" required>
                    <option value="" disabled selected>-- Seleccione un rol --</option>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?php echo $rol->id; ?>" <?php echo $usuario->id_rol === $rol->id ? 'selected' : ''; ?>>
                            <?php echo $rol->nombre; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="pwd" class="block text-gray-700 font-medium mb-1">Contraseña <span class="text-red-500">*</span></label>
                <input class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300 ease-in-out" type="password" name="pwd" id="pwd" placeholder="Ej: ********" required />
            </div>
            <div>
                <button class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition duration-300 ease-in-out transform cursor-pointer hover:-translate-y-1">Registrar Usuario</button>
            </div>
        </form>

    </div>
</section>