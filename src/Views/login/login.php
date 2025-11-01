<section class="bg-gradient-to-br from-[#2563eb]/10 to-[#1e40af]/20 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-xl shadow-2xl overflow-hidden relative">
        <!-- Efecto decorativo -->
        <div class="absolute -right-20 -top-20 w-40 h-40 bg-[#2563eb]/20 rounded-full"></div>
        <div class="absolute -left-10 -bottom-10 w-60 h-60 bg-[#1e40af]/10 rounded-full"></div>

        <!-- Contenido principal -->
        <div class="relative z-10 p-8">
            <!-- Encabezado -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-[#1e40af] mb-2">Bienvenido</h1>
                <p class="text-[#2563eb]">Ingresa tus credenciales para acceder</p>
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

            <!-- Formulario -->
            <form class="space-y-6" method="POST">
                <!-- Campo Usuario -->
                <div class="space-y-2">
                    <label for="nombre_usuario" class="block text-sm font-medium text-[#1e40af]">Usuario</label>
                    <div class="relative">
                        <input
                            id="nombre_usuario"
                            type="text"
                            name="nombre_usuario"
                            placeholder="Ej. usuario123"
                            required
                            class="w-full px-4 py-3 border-b-2 border-[#2563eb]/30 focus:border-[#1e40af] focus:outline-none bg-transparent transition-colors duration-300 placeholder:text-[#2563eb]/60" />
                        <div class="absolute bottom-0 left-0 w-0 h-[2px] bg-[#1e40af] transition-all duration-300 group-focus-within:w-full"></div>
                    </div>
                </div>

                <!-- Campo Contraseña -->
                <div class="space-y-2">
                    <label for="pwd" class="block text-sm font-medium text-[#1e40af]">Contraseña</label>
                    <div class="relative">
                        <input
                            id="pwd"
                            type="password"
                            name="pwd"
                            placeholder="••••••••"
                            required
                            class="w-full px-4 py-3 border-b-2 border-[#2563eb]/30 focus:border-[#1e40af] focus:outline-none bg-transparent transition-colors duration-300 placeholder:text-[#2563eb]/60" />
                        <button id="togglePassword" type="button" class="absolute right-3 top-3 text-[#2563eb]/60 hover:text-[#1e40af]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Botón de Login -->
                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-[#2563eb] to-[#1e40af] text-white py-3 px-4 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-300 hover:from-[#1e40af] hover:to-[#2563eb] focus:outline-none focus:ring-2 focus:ring-[#2563eb] focus:ring-opacity-50">
                    Iniciar Sesión
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Script para mostrar/ocultar contraseña -->
<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('pwd');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('svg').classList.toggle('text-[#1e40af]');
    });

    document.querySelector('form').addEventListener('submit', function(e) {
        const btn = this.querySelector('button[type="submit"]');
        btn.innerHTML = '<span class="inline-block animate-spin">⏳</span> Procesando...';
        btn.disabled = true;
    });
</script>