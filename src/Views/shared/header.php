<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost Nexus</title>
    <link rel="stylesheet" href="/css/main.css" />
</head>

<body class="font-display flex flex-col min-h-screen bg-gray-200">
    <header class="bg-(--color-primary) shadow-lg">
        <div class="mx-auto flex items-center justify-between px-4 py-3 sm:px-6 lg:px-8 max-w-7xl">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="/" class="flex items-center">
                    <img class="h-8 w-auto sm:h-10 md:h-12 lg:h-14 transition-all duration-300 hover:scale-105"
                        src="/img/lost_found.png" alt="Logo">
                </a>
            </div>

            <!-- Menú Desktop -->
            <div class="hidden md:flex items-center ml-6">

                <nav class="flex items-center gap-4">

                    <div class="relative group">
                        <button class="nav-link group flex items-center gap-1 px-2 py-1 text-sm sm:text-base cursor-pointer">
                            Objetos
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            <span class="nav-link-span group-hover:w-full"></span>
                        </button>
                        <div class="absolute left-0 w-40 bg-white rounded-md shadow-lg py-1 z-50 hidden group-hover:block">
                            <a href="/objetosperdidos" class="block px-3 py-1.5 text-sm text-gray-800 hover:bg-gray-100">Objetos perdidos</a>
                            <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true): ?>
                                <?php if (!isset($_SESSION['rol_nombre']) || $_SESSION['rol_nombre'] !== 'Estudiante'): ?>
                                    <a href="/agregar_objetos" class="block px-3 py-1.5 text-sm text-gray-800 hover:bg-gray-100">Agregar Objetos</a>
                                    <a href="/objetosdevueltos" class="block px-3 py-1.5 text-sm text-gray-800 hover:bg-gray-100">Objetos devueltos</a>
                                    <a href="/objetosperdidos/archivados" class="block px-3 py-1.5 text-sm text-gray-800 hover:bg-gray-100">Objetos archivados</a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true && (!isset($_SESSION['rol_nombre']) || $_SESSION['rol_nombre'] !== 'Estudiante')): ?>
                        <div class="relative group">
                            <button class="nav-link group flex items-center gap-1 px-2 py-1 text-sm sm:text-base cursor-pointer">
                                Categorias
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                <span class="nav-link-span group-hover:w-full"></span>
                            </button>

                            <div class="absolute left-0 w-40 bg-white rounded-md shadow-lg py-1 z-50 hidden group-hover:block">
                                <a class="block px-3 py-1.5 text-sm text-gray-800 hover:bg-gray-100" href="/categorias">Ver Categorias</a>
                                <a class="block px-3 py-1.5 text-sm text-gray-800 hover:bg-gray-100" href="/agregar_categoria">Agregar Categoria</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true && (!isset($_SESSION['rol_nombre']) || $_SESSION['rol_nombre'] !== 'Estudiante')): ?>
                        <div class="relative group">
                            <button class="nav-link group flex items-center gap-1 px-2 py-1 text-sm sm:text-base cursor-pointer">
                                Puntos
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                <span class="nav-link-span group-hover:w-full"></span>
                            </button>

                            <div class="absolute left-0 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden group-hover:block">
                                <a class="block px-3 py-1.5 text-sm text-gray-800 hover:bg-gray-100" href="/puntosrecepcion">Ver Puntos</a>
                                <a class="block px-3 py-1.5 text-sm text-gray-800 hover:bg-gray-100" href="/agregar_punto_recepcion">Agregar Punto</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true): ?>
                        <div class="relative group ml-2">
                            <button class="nav-link group flex items-center gap-1 px-2 py-1 text-sm sm:text-base cursor-pointer">
                                <?php echo explode(' ', $_SESSION['nombre'])[0]; ?>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                <span class="nav-link-span group-hover:w-full"></span>
                            </button>
                            <div class="absolute right-0 w-40 bg-white rounded-md shadow-lg py-1 z-50 hidden group-hover:block">
                                <?php if (!isset($_SESSION['rol_nombre']) || $_SESSION['rol_nombre'] !== 'Estudiante'): ?>
                                    <a class="block px-3 py-1.5 text-sm text-gray-800 hover:bg-gray-100" href="/admin/usuarios">Usuarios</a>
                                    <a class="block px-3 py-1.5 text-sm text-gray-800 hover:bg-gray-100" href="/admin/usuarios/crear">Agregar Usuario</a>
                                <?php endif; ?>
                                <a class="block px-3 py-1.5 text-sm text-gray-800 hover:bg-gray-100" href="/logout">Salir</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!isset($_SESSION['login']) || $_SESSION['login'] !== true): ?>
                        <a href="/login" class="nav-link group px-2 py-1 text-sm sm:text-base">
                            Ingresar
                            <span class="nav-link-span group-hover:w-full"></span>
                        </a>
                    <?php endif; ?>
                </nav>
            </div>

            <!-- Botón Móvil -->
            <button class="md:hidden text-white focus:outline-none" id="mobile-btn">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>

        <!-- Menú Móvil -->
        <div id="mobile-menu" class="md:hidden hidden bg-[--color-primary]/25 shadow-lg backdrop-blur-sm transition-all duration-300">
            <div class="flex flex-col space-y-1 px-2 pb-3 pt-2">
                <a href="/objetosperdidos" class="nav-link-mobile">
                    Objetos Perdidos
                </a>

                <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true): ?>
                    <?php if (!isset($_SESSION['rol_nombre']) || $_SESSION['rol_nombre'] !== 'Estudiante'): ?>
                        <a href="/agregar_objetos" class="nav-link-mobile">
                            Agregar Objetos
                        </a>
                        <a href="/objetosdevueltos" class="nav-link-mobile">
                            Ver Objetos devueltos
                        </a>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true && (!isset($_SESSION['rol_nombre']) || $_SESSION['rol_nombre'] !== 'Estudiante')): ?>
                    <details class="group">
                        <summary class="nav-link-mobile flex justify-between items-center list-none cursor-pointer">
                            <span>Categorias</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </summary>
                        <div class="pl-4">
                            <a href="/categorias" class="nav-link-mobile block py-2 text-sm">Ver Categorias</a>
                            <a href="/agregar_categoria" class="nav-link-mobile block py-2 text-sm">Agregar Categoria</a>
                        </div>
                    </details>
                <?php endif; ?>

                <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true && (!isset($_SESSION['rol_nombre']) || $_SESSION['rol_nombre'] !== 'Estudiante')): ?>
                    <details class="group">
                        <summary class="nav-link-mobile flex justify-between items-center list-none cursor-pointer">
                            <span>Puntos de Recepción</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </summary>
                        <div class="pl-4">
                            <a href="/puntos_recepcion" class="nav-link-mobile block py-2 text-sm">Ver Puntos de Recepción</a>
                            <a href="/agregar_punto_recepcion" class="nav-link-mobile block py-2 text-sm">Agregar Punto de Recepción</a>
                        </div>
                    </details>
                <?php endif; ?>

                <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true): ?>
                    <details class="group">
                        <summary class="nav-link-mobile flex justify-between items-center list-none cursor-pointer">
                            <span><?php echo $_SESSION['nombre']; ?></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </summary>
                        <div class="pl-4">
                            <?php if (!isset($_SESSION['rol_nombre']) || $_SESSION['rol_nombre'] !== 'Estudiante'): ?>
                                <a href="/admin/usuarios" class="nav-link-mobile block py-2 text-sm">Ver Usuarios</a>
                                <a href="/admin/usuarios/crear" class="nav-link-mobile block py-2 text-sm">Agregar Usuario</a>
                            <?php endif; ?>
                            <a href="/logout" class="nav-link-mobile block py-2 text-sm">Cerrar Sesión</a>
                        </div>
                    </details>
                <?php endif; ?>

                <?php if (!isset($_SESSION['login']) || $_SESSION['login'] !== true): ?>
                    <a href="/login" class="nav-link-mobile">
                        Iniciar Sesión
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="flex-grow bg-(--color-bg)/10">