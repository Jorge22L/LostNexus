<?php

namespace App\Controllers;

use App\Helpers\CSRF;
use App\Models\Categoria;
use App\Models\Objeto;
use App\Models\PuntoRecepcion;
use App\Models\Reclamacion;
use App\Models\Reclamante;
use App\Router;
use DateTime;
use Exception;

class ObjetosPerdidosController
{
    public static function index(Router $router)
    {
        $filtros = [
            'nombre' => trim($_GET['nombre'] ?? ''),
            'categoria' => $_GET['categoria'] ?? null,
            'fecha' => trim($_GET['fecha'] ?? '')
        ];

        // Validación más robusta de categoría
        $filtros['categoria'] = ($filtros['categoria'] === '' || $filtros['categoria'] === null || !is_numeric($filtros['categoria']))
            ? null
            : (int)$filtros['categoria'];

        // Validación mejorada de fecha (corrección definitiva)
        if ($filtros['fecha'] !== '') {
            try {
                $dateObj = new DateTime($filtros['fecha']);
                $filtros['fecha'] = $dateObj->format('Y-m-d');
            } catch (Exception $e) {
                $filtros['fecha'] = null;
            }
        }

        // Paginación con valores por defecto seguros
        $paginaActual = max(1, (int)($_GET['page'] ?? 1));
        $registrosPorPagina = 10; // Valor fijo seguro

        if (!method_exists('App\Models\Objeto', 'filtrar')) {
            error_log("ERROR: Método filtrar no existe en Objeto");
        } else {
            error_log("Método filtrar existe");
        }

        try {
            // Por defecto, siempre mostrar los objetos de los últimos 2 meses.
            // Los objetos más antiguos están en la sección 'archivados'.
            $objetos = Objeto::filtrar($filtros, $registrosPorPagina, $paginaActual, true);

            // Contar registros con el mismo filtro de fecha.
            $totalRegistros = (int)Objeto::contarFiltrados($filtros, true);
            $totalRegistros = max(0, $totalRegistros); // Nunca negativo

            // Cálculo seguro de páginas
            $totalPaginas = ($registrosPorPagina > 0)
                ? max(1, ceil($totalRegistros / $registrosPorPagina))
                : 1;

            $router->render('objetosperdidos/index', [
                'objetos' => $objetos,
                'categorias' => Categoria::all(),
                'paginaActual' => $paginaActual,
                'totalPaginas' => $totalPaginas,
                'nombre_filtro' => $filtros['nombre'],
                'categoria_filtro' => $filtros['categoria'],
                'fecha_filtro' => $filtros['fecha']
            ]);
        } catch (Exception $e) {
            // Manejo de errores mejorado
            error_log("Error en ObjetosPerdidosController: " . $e->getMessage());
            $_SESSION['error'] = "Error al cargar los objetos perdidos";
            header('Location: /error');
            exit;
        }
    }

    public static function crear(Router $router)
    {
        $alertas = [];
        $objeto = new Objeto();

        // Obtener categorias
        $categorias = Categoria::all();

        // Obtener puntos de recepcion
        $puntosRecepcion = PuntoRecepcion::all();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar CSRF
            if (!isset($_POST['_csrf']) || !CSRF::validateToken($_POST['_csrf'])) {
                $alertas['error'][] = 'Token CSRF no válido';
                $router->render('/objetosperdidos/agregar_objetos', [
                    'alertas' => $alertas,
                    'objeto' => $objeto,
                    'categorias' => $categorias,
                    'puntosRecepcion' => $puntosRecepcion
                ]);
            }
            // Procesar los datos del formulario
            $datos = $_POST;

            // Asignar valores automáticos
            $datos['usuario_guarda'] = $_SESSION['id'];
            $datos['fecha_reporte'] = date('Y-m-d H:i:s');
            $datos['estado'] = 'Perdido';

            $imagen = $_FILES['foto'];
            $medida = 500 * 500;

            if ($imagen['size'] > $medida) {
                $alertas['error'][] = 'La imagen es muy grande';
            }

            // Crear y guardar el objeto
            $objeto = new Objeto($datos);

            $alertas = $objeto->validar();

            if (empty($alertas['error'])) {
                /** Subida de archivos */
                $carpeta = __DIR__ . '../../../public/imagenes/';
                if (!is_dir($carpeta)) {
                    mkdir($carpeta);
                }

                // Generar un nombre único para la imagen
                $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";

                // Subir la imagen
                move_uploaded_file($imagen['tmp_name'], $carpeta . $nombreImagen);

                $objeto->foto = $nombreImagen;

                $resultado = $objeto->guardar();
                if ($resultado) {
                    header('Location: /objetosperdidos');
                    exit;
                } else {
                    $alertas['error'][] = 'Error al guardar el objeto';
                }
            }
        }

        $router->render('/objetosperdidos/agregar_objetos', [
            'alertas' => $alertas,
            'objeto' => $objeto,
            'categorias' => $categorias,
            'puntosRecepcion' => $puntosRecepcion
        ]);
    }

    public static function ver(Router $router, $id = null)
    {
        if (!$id) {
            header('Location: /objetosperdidos');
            exit;
        }
        // Buscar el objeto en la base de datos
        $objeto = Objeto::findWithDetails($id);

        $router->render('/objetosperdidos/ver_objeto_perdido', [
            'objeto' => $objeto
        ]);
    }

    public static function editar(Router $router, $id)
    {
        // Obtener el objeto existente
        $objeto = Objeto::find($id);

        //Recuperar categorias
        $categorias = Categoria::all();

        //Recuperar puntos de recepcion
        $puntosRecepcion = PuntoRecepcion::all();

        if (!$objeto) {
            header('Location: /objetosperdidos');
            exit;
        }

        $alertas = [];
        $carpeta = __DIR__ . '../../../public/imagenes/';
        $imagenAnterior = $objeto->foto;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar CSRF
            if (!isset($_POST['_csrf']) || !CSRF::validateToken($_POST['_csrf'])) {
                $alertas['error'][] = 'Token CSRF no válido';
                $router->render('/objetosperdidos/editar', [
                    'alertas' => $alertas,
                    'objeto' => $objeto,
                    'categorias' => $categorias,
                    'puntosRecepcion' => $puntosRecepcion
                ]);
                return;
            }
            // Sincronizar con datos del formulario
            $objeto->sincronizar($_POST);

            // Validar campos
            $alertas = $objeto->validar();

            // Procesar imagen si se envió
            if ($_FILES['foto']['tmp_name']) {
                $imagen = $_FILES['foto'];
                $medida = 1000 * 1000;

                if ($imagen['size'] > $medida) {
                    $alertas['error'][] = 'La imagen es muy grande';
                }

                if (empty($alertas['error'])) {
                    // Generar nombre único (igual que en crear)
                    $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";

                    // Subir la imagen (sin validaciones adicionales)
                    move_uploaded_file($imagen['tmp_name'], $carpeta . $nombreImagen);

                    // Eliminar imagen anterior si existe
                    if ($imagenAnterior && file_exists($carpeta . $imagenAnterior)) {
                        unlink($carpeta . $imagenAnterior);
                    }

                    $objeto->foto = $nombreImagen;
                }
            }

            if (empty($alertas['error'])) {
                $resultado = $objeto->actualizar();

                if ($resultado) {
                    header('Location: /objetosperdidos/ver/' . $objeto->id);
                    exit;
                } else {
                    $alertas['error'][] = 'Error al actualizar el objeto';
                }
            }
        }

        $router->render('/objetosperdidos/editar', [
            'alertas' => $alertas,
            'objeto' => $objeto,
            'categorias' => $categorias,
            'puntosRecepcion' => $puntosRecepcion
        ]);
    }

    public static function archivados(Router $router)
    {
        if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 'admin') {
            header('Location: /');
            exit;
        }

        $filtros = [
            'nombre' => trim($_GET['nombre'] ?? ''),
            'categoria' => $_GET['categoria'] ?? null,
            'fecha' => trim($_GET['fecha'] ?? '')
        ];

        $filtros['categoria'] = ($filtros['categoria'] === '' || $filtros['categoria'] === null || !is_numeric($filtros['categoria']))
            ? null
            : (int)$filtros['categoria'];

        if ($filtros['fecha'] !== '') {
            try {
                $dateObj = new DateTime($filtros['fecha']);
                $filtros['fecha'] = $dateObj->format('Y-m-d');
            } catch (Exception $e) {
                $filtros['fecha'] = null;
            }
        }

        $paginaActual = max(1, (int)($_GET['page'] ?? 1));
        $registrosPorPagina = 10;

        try {
            $objetos = Objeto::filtrarArchivados($filtros, $registrosPorPagina, $paginaActual);
            $totalRegistros = (int)Objeto::contarArchivados($filtros);
            $totalRegistros = max(0, $totalRegistros);

            $totalPaginas = ($registrosPorPagina > 0)
                ? max(1, ceil($totalRegistros / $registrosPorPagina))
                : 1;

            $router->render('objetosperdidos/archivados', [
                'objetos' => $objetos,
                'categorias' => Categoria::all(),
                'totalPaginas' => $totalPaginas,
                'paginaActual' => $paginaActual,
                'nombre_filtro' => $filtros['nombre'],
                'categoria_filtro' => $filtros['categoria'],
                'fecha_filtro' => $filtros['fecha']
            ]);
        } catch (Exception $e) {
            error_log("Error en ObjetosPerdidosController (archivados): " . $e->getMessage());
            $_SESSION['error'] = "Error al cargar los objetos archivados";
            header('Location: /error');
            exit;
        }
    }

    public static function darDeBaja(Router $router, $id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 'admin') {
                header('Location: /');
                exit;
            }

            if (!$id) {
                header('Location: /objetosperdidos/archivados');
                exit;
            }

            $objeto = Objeto::find($id);
            if ($objeto) {
                $objeto->estado = 'baja';
                $resultado = $objeto->guardar();

                if ($resultado) {
                    $_SESSION['alerta'] = ['tipo' => 'exito', 'mensaje' => 'El objeto ha sido dado de baja correctamente.'];
                } else {
                    $_SESSION['alerta'] = ['tipo' => 'error', 'mensaje' => 'Error al dar de baja el objeto.'];
                }
            }
            header('Location: /objetosperdidos/archivados');
            exit;
        }
    }

    public static function devolver(Router $router, $id)
    {
        // Obtener el objeto existente
        // Buscar el objeto en la base de datos
        $objeto = Objeto::findWithDetails($id);

        if (!$objeto) {
            header('Location: /objetosperdidos');
            exit;
        }

        $alertas = [];
        $carpeta = __DIR__ . '../../../public/evidencia/';

        // Verificar y crear directorio si no existe
        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0755, true);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar CSRF
            if (!isset($_POST['_csrf']) || !CSRF::validateToken($_POST['_csrf'])) {
                $alertas['error'][] = 'Token CSRF no válido';
                $router->render('/objetosperdidos/devolver', [
                    'alertas' => $alertas,
                    'objeto' => $objeto
                ]);
                return;
            }
            // 1. Validar que el usuario_atiende existe y es numérico
            if (!isset($_SESSION['id']) || !is_numeric($_SESSION['id'])) {
                $alertas['error'][] = 'No se pudo identificar al usuario que realiza la devolución';
            } else {
                $usuarioAtiende = (int)$_SESSION['id'];
            }

            // 2. Crear datos del dueño o reclamante
            $reclamante = new Reclamante([
                'nombre' => trim($_POST['nombre']),
                'apellido' => trim($_POST['apellido']),
                'cedula' => trim($_POST['cedula']),
                'carnet_estudiante' => trim($_POST['carnet_estudiante'] ?? ''),
                'email' => trim($_POST['email']),
                'fecha_registro' => date('Y-m-d H:i:s'),
            ]);

            // 3. Validar datos del dueño o reclamante
            $alertas = array_merge($alertas, $reclamante->validarReclamante());

            // 4. Procesar la imagen de evidencia
            $evidencia = null;
            if (empty($_FILES['evidencia']['tmp_name'])) {
                $alertas['error'][] = 'La evidencia es obligatoria';
            } else {
                $imagen = $_FILES['evidencia'];
                $medida = 1000 * 1000; // 1MB máximo

                if ($imagen['size'] > $medida) {
                    $alertas['error'][] = 'La imagen es muy grande (Máximo 1MB permitido)';
                }

                // Validar tipo de imagen
                $tipoPermitidos = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!in_array($imagen['type'], $tipoPermitidos)) {
                    $alertas['error'][] = 'Formato de imagen no válido (Solo JPG, JPEG o PNG)';
                }

                if (empty($alertas['error'])) {
                    $evidencia = md5(uniqid(rand(), true)) . ".jpg";
                    if (!move_uploaded_file($imagen['tmp_name'], $carpeta . $evidencia)) {
                        $alertas['error'][] = 'Error al guardar la evidencia';
                    }
                }
            }

            // 5. Guardar si no hay errores
            if (empty($alertas['error'])) {
                    // Guardar datos del dueño o reclamante
                    $resultado = $reclamante->guardar();

                    if ($resultado['resultado']) {
                        $reclamacion = new Reclamacion([
                            'idobjeto' => $objeto->id,
                            'idreclamante' => $resultado['id'],
                            'fecha_reclamacion' => date('Y-m-d H:i:s'),
                            'observaciones' => trim($_POST['observaciones']),
                            'usuario_atiende' => $usuarioAtiende, // Usamos el ID de sesión validado
                            'evidencia' => $evidencia
                        ]);

                        // Validar reclamación
                        $alertasReclamacion = $reclamacion->validarReclamacion();

                        if (empty($alertasReclamacion['error'])) {
                            // Guardar la reclamación
                            $resultadoReclamacion = $reclamacion->guardar();

                            if ($resultadoReclamacion['resultado']) {
                                // Actualizando estado del objeto a Devuelto
                                $objeto->estado = 'Devuelto';
                                $objeto->fecha_devolucion = date('Y-m-d H:i:s');
                                $objeto->usuario_devuelve = $usuarioAtiende;
                                $resultadoActualizacion = $objeto->actualizar();

                                if ($resultadoActualizacion) {
                                    header('Location: /objetosdevueltos/ver/' . $objeto->id);
                                    exit;
                                } else {
                                    $alertas['error'][] = 'Error al actualizar el estado del objeto';
                                }
                            } else {
                                $alertas['error'][] = 'Error al guardar la reclamación';
                                // Eliminar imagen si falló
                                if (file_exists($carpeta . $evidencia)) {
                                    unlink($carpeta . $evidencia);
                                }
                            }
                        } else {
                            $alertas['error'] = array_merge($alertas['error'], $alertasReclamacion['error']);
                            // Eliminar imagen si hay errores de validación
                            if ($evidencia && file_exists($carpeta . $evidencia)) {
                                unlink($carpeta . $evidencia);
                            }
                        }
                    } else {
                        $alertas['error'][] = 'Error al registrar el reclamante';
                    }
                }
            }

        $router->render('/objetosperdidos/devolucion', [
            'alertas' => $alertas,
            'objeto' => $objeto
        ]);
    }
}
