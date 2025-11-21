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

    public function __construct()
    {
        $this->configurarUploadTmpDir();
    }

    private function configurarUploadTmpDir()
    {
        $tempDir = sys_get_temp_dir() . '/php-uploads';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }


        ini_set('upload_tmp_dir', $tempDir);


        if (ini_get('upload_tmp_dir') !== $tempDir) {
            error_log("ERROR: No se pudo configurar upload_tmp_dir");
        }
    }

    public static function index(Router $router)
    {
        $filtros = [
            'nombre' => trim($_GET['nombre'] ?? ''),
            'categoria' => $_GET['categoria'] ?? null,
            'fecha' => trim($_GET['fecha'] ?? '')
        ];

        // Validaci�n m�s robusta de categor�a
        $filtros['categoria'] = ($filtros['categoria'] === '' || $filtros['categoria'] === null || !is_numeric($filtros['categoria']))
            ? null
            : (int)$filtros['categoria'];

        // Validaci�n mejorada de fecha (correcci�n definitiva)
        if ($filtros['fecha'] !== '') {
            try {
                $dateObj = new DateTime($filtros['fecha']);
                $filtros['fecha'] = $dateObj->format('Y-m-d');
            } catch (Exception $e) {
                $filtros['fecha'] = null;
            }
        }

        // Paginaci�n con valores por defecto seguros
        $paginaActual = max(1, (int)($_GET['page'] ?? 1));
        $registrosPorPagina = 10; // Valor fijo seguro

        if (!method_exists('App\Models\Objeto', 'filtrar')) {
            error_log("ERROR: M�todo filtrar no existe en Objeto");
        } else {
            error_log("M�todo filtrar existe");
        }

        try {
            // Por defecto, siempre mostrar los objetos de los �ltimos 2 meses.
            // Los objetos m�s antiguos est�n en la secci�n 'archivados'.
            $objetos = Objeto::filtrar($filtros, $registrosPorPagina, $paginaActual, true);

            // Contar registros con el mismo filtro de fecha.
            $totalRegistros = (int)Objeto::contarFiltrados($filtros, true);
            $totalRegistros = max(0, $totalRegistros); // Nunca negativo

            // C�lculo seguro de p�ginas
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

        // SOLUCI�N: Verificar configuraci�n de subida al inicio
        self::verificarConfiguracionUpload($alertas);

        // Obtener categorias
        $categorias = Categoria::all();

        // Obtener puntos de recepcion
        $puntosRecepcion = PuntoRecepcion::all();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar CSRF
            if (!isset($_POST['_csrf']) || !CSRF::validateToken($_POST['_csrf'])) {
                $alertas['error'][] = 'Token CSRF no v�lido';
                $router->render('/objetosperdidos/agregar_objetos', [
                    'alertas' => $alertas,
                    'objeto' => $objeto,
                    'categorias' => $categorias,
                    'puntosRecepcion' => $puntosRecepcion
                ]);
            }

            // SOLUCI�N: Verificar error de subida antes de procesar
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
                $errorMsg = self::getUploadError($_FILES['foto']['error']);
                $alertas['error'][] = "Error al subir imagen: " . $errorMsg;
            }

            // Procesar los datos del formulario
            $datos = $_POST;

            // Asignar valores autom�ticos
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
                $carpeta = '/var/www/lost_nexus/public/imagenes/';

                // SOLUCI�N: Crear directorio si no existe con permisos
                if (!is_dir($carpeta)) {
                    if (!mkdir($carpeta, 0755, true)) {
                        $alertas['error'][] = 'No se pudo crear el directorio de im�genes';
                    }
                }

                if (empty($alertas['error'])) {
                    // Generar un nombre �nico para la imagen
                    $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";

                    // SOLUCI�N: Verificar que el archivo temporal existe antes de moverlo
                    if (!file_exists($imagen['tmp_name'])) {
                        $alertas['error'][] = 'El archivo temporal no existe. Error de configuraci�n del servidor.';
                    } else {
                        // Subir la imagen
                        if (move_uploaded_file($imagen['tmp_name'], $carpeta . $nombreImagen)) {
                            $objeto->foto = $nombreImagen;
                            $resultado = $objeto->guardar();

                            if ($resultado) {
                                header('Location: /objetosperdidos');
                                exit;
                            } else {
                                $alertas['error'][] = 'Error al guardar el objeto';
                                // Eliminar imagen si fall� el guardado
                                if (file_exists($carpeta . $nombreImagen)) {
                                    unlink($carpeta . $nombreImagen);
                                }
                            }
                        } else {
                            $alertas['error'][] = 'Error al mover el archivo subido';
                            $error = error_get_last();
                            if ($error) {
                                $alertas['error'][] = $error['message'];
                            }
                        }
                    }
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
        $carpeta = '/var/www/lost_nexus/public/imagenes/';
        $imagenAnterior = $objeto->foto;

        // SOLUCI�N: Verificar configuraci�n
        self::verificarConfiguracionUpload($alertas);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar CSRF
            if (!isset($_POST['_csrf']) || !CSRF::validateToken($_POST['_csrf'])) {
                $alertas['error'][] = 'Token CSRF no v�lido';
                $router->render('/objetosperdidos/editar', [
                    'alertas' => $alertas,
                    'objeto' => $objeto,
                    'categorias' => $categorias,
                    'puntosRecepcion' => $puntosRecepcion
                ]);
                return;
            }

            // SOLUCI�N: Verificar error de subida
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_OK && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
                $errorMsg = self::getUploadError($_FILES['foto']['error']);
                $alertas['error'][] = "Error al subir imagen: " . $errorMsg;
            }

            // Sincronizar con datos del formulario
            $objeto->sincronizar($_POST);

            // Validar campos
            $alertas = $objeto->validar();

            // Procesar imagen si se envi�
            if ($_FILES['foto']['tmp_name']) {
                $imagen = $_FILES['foto'];
                $medida = 1000 * 1000;

                if ($imagen['size'] > $medida) {
                    $alertas['error'][] = 'La imagen es muy grande';
                }

                if (empty($alertas['error'])) {
                    // Generar nombre �nico (igual que en crear)
                    $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";

                    // Subir la imagen (sin validaciones adicionales)
                    if (move_uploaded_file($imagen['tmp_name'], $carpeta . $nombreImagen)) {
                        // Eliminar imagen anterior si existe
                        if ($imagenAnterior && file_exists($carpeta . $imagenAnterior)) {
                            unlink($carpeta . $imagenAnterior);
                        }
                        $objeto->foto = $nombreImagen;
                    } else {
                        $alertas['error'][] = 'Error al mover el archivo subido';
                    }
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
        // Obtener el objeto existente
        $objeto = Objeto::findWithDetails($id);

        if (!$objeto) {
            header('Location: /objetosperdidos');
            exit;
        }

        $alertas = [];
        $carpeta = __DIR__ . '/../../../public/evidencia/';

        // SOLUCI�N: Verificar configuraci�n
        self::verificarConfiguracionUpload($alertas);

        // Verificar y crear directorio si no existe
        if (!is_dir($carpeta)) {
            if (!mkdir($carpeta, 0755, true)) {
                $alertas['error'][] = 'No se pudo crear el directorio de evidencia';
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar CSRF
            if (!isset($_POST['_csrf']) || !CSRF::validateToken($_POST['_csrf'])) {
                $alertas['error'][] = 'Token CSRF no v�lido';
                $router->render('/objetosperdidos/devolver', [
                    'alertas' => $alertas,
                    'objeto' => $objeto
                ]);
                return;
            }

            // SOLUCI�N: Verificar error de subida
            if (isset($_FILES['evidencia']) && $_FILES['evidencia']['error'] !== UPLOAD_ERR_OK) {
                $errorMsg = self::getUploadError($_FILES['evidencia']['error']);
                $alertas['error'][] = "Error al subir evidencia: " . $errorMsg;
            }

            // 1. Validar que el usuario_atiende existe y es num�rico
            if (!isset($_SESSION['id']) || !is_numeric($_SESSION['id'])) {
                $alertas['error'][] = 'No se pudo identificar al usuario que realiza la devoluci�n';
            } else {
                $usuarioAtiende = (int)$_SESSION['id'];
            }

            // 2. Crear datos del due�o o reclamante
            $reclamante = new Reclamante([
                'nombre' => trim($_POST['nombre']),
                'apellido' => trim($_POST['apellido']),
                'cedula' => trim($_POST['cedula']),
                'carnet_estudiante' => trim($_POST['carnet_estudiante'] ?? ''),
                'email' => trim($_POST['email']),
                'fecha_registro' => date('Y-m-d H:i:s'),
            ]);

            // 3. Validar datos del due�o o reclamante
            $alertas = array_merge($alertas, $reclamante->validarReclamante());

            // 4. Procesar la imagen de evidencia
            $evidencia = null;
            if (empty($_FILES['evidencia']['tmp_name'])) {
                $alertas['error'][] = 'La evidencia es obligatoria';
            } else {
                $imagen = $_FILES['evidencia'];
                $medida = 1000 * 1000; // 1MB m�ximo

                if ($imagen['size'] > $medida) {
                    $alertas['error'][] = 'La imagen es muy grande (M�ximo 1MB permitido)';
                }

                // Validar tipo de imagen
                $tipoPermitidos = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!in_array($imagen['type'], $tipoPermitidos)) {
                    $alertas['error'][] = 'Formato de imagen no v�lido (Solo JPG, JPEG o PNG)';
                }

                if (empty($alertas['error'])) {
                    $evidencia = md5(uniqid(rand(), true)) . ".jpg";
                    if (!move_uploaded_file($imagen['tmp_name'], $carpeta . $evidencia)) {
                        $alertas['error'][] = 'Error al guardar la evidencia';
                        $error = error_get_last();
                        if ($error) {
                            $alertas['error'][] = $error['message'];
                        }
                    }
                }
            }

            // 5. Guardar si no hay errores
            if (empty($alertas['error'])) {
                // Guardar datos del due�o o reclamante
                $resultado = $reclamante->guardar();

                if ($resultado['resultado']) {
                    $reclamacion = new Reclamacion([
                        'idobjeto' => $objeto->id,
                        'idreclamante' => $resultado['id'],
                        'fecha_reclamacion' => date('Y-m-d H:i:s'),
                        'observaciones' => trim($_POST['observaciones']),
                        'usuario_atiende' => $usuarioAtiende, // Usamos el ID de sesi�n validado
                        'evidencia' => $evidencia
                    ]);

                    // Validar reclamaci�n
                    $alertasReclamacion = $reclamacion->validarReclamacion();

                    if (empty($alertasReclamacion['error'])) {
                        // Guardar la reclamaci�n
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
                            $alertas['error'][] = 'Error al guardar la reclamaci�n';
                            // Eliminar imagen si fall�
                            if (file_exists($carpeta . $evidencia)) {
                                unlink($carpeta . $evidencia);
                            }
                        }
                    } else {
                        $alertas['error'] = array_merge($alertas['error'], $alertasReclamacion['error']);
                        // Eliminar imagen si hay errores de validaci�n
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

    private static function verificarConfiguracionUpload(&$alertas)
    {
        // Directorio temporal dentro del proyecto - CORREGIDO
        $tempDir = '/var/www/lost_nexus/tmp';

        if (!is_dir($tempDir)) {
            if (!mkdir($tempDir, 0755, true)) {
                $alertas['error'][] = 'No se pudo crear directorio temporal en: ' . $tempDir;
                error_log("ERROR: No se pudo crear directorio temporal: " . $tempDir);
                return false;
            }
        }

        // Configurar PHP para usar este directorio
        ini_set('upload_tmp_dir', $tempDir);

        // Verificar que se configur� correctamente
        $configuredDir = ini_get('upload_tmp_dir');
        if ($configuredDir !== $tempDir) {
            error_log("ADVERTENCIA: upload_tmp_dir no se configur�. Esperado: $tempDir, Actual: $configuredDir");
        }

        // Verificar permisos de escritura
        if (!is_writable($tempDir)) {
            $alertas['error'][] = 'Directorio temporal no tiene permisos de escritura: ' . $tempDir;
            error_log("ERROR: Directorio temporal no escribible: " . $tempDir);
            return false;
        }

        // Verificar que file_uploads est� habilitado
        if (!ini_get('file_uploads')) {
            $alertas['error'][] = 'File uploads est�n deshabilitados en PHP';
            return false;
        }

        error_log("Configuraci�n upload correcta. Temp dir: " . $tempDir);
        return true;
    }

    private static function getUploadError($errorCode)
    {
        $uploadErrors = [
            UPLOAD_ERR_OK => 'No hay error',
            UPLOAD_ERR_INI_SIZE => 'Tamaño excede upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'Tamaño excede MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'Archivo parcialmente subido',
            UPLOAD_ERR_NO_FILE => 'No se subió ningun archivo',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta carpeta temporal',
            UPLOAD_ERR_CANT_WRITE => 'Fallo escritura en disco',
            UPLOAD_ERR_EXTENSION => 'Una extension de PHP detuvo la subida'
        ];

        return $uploadErrors[$errorCode] ?? 'Error desconocido';
    }
}
