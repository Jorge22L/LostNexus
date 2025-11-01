<div class="container mx-auto px-4 py-8">
    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Panel de estadísticas</h1>
        <div class="text-md font-semibold text-gray-500 ">
            Bienvenido,
            <?php echo htmlspecialchars($_SESSION['nombre_usuario'] ?? 'Usuario'); ?>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Objetos Pendientes -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <h3 class="text-gray-500 text-sm font-medium">Pendientes de entrega</h3>
            <p class="text-3xl font-bold text-gray-800"><?php echo $totalPendientes; ?></p>
            <a href="/objetosperdidos" class="text-blue-500 text-sm hover:underline mt-2 block">Ver todos</a>
        </div>

        <!-- Objetos Devueltos -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <h3 class="text-gray-500 text-sm font-medium">Total devueltos</h3>
            <p class="text-3xl font-bold text-gray-800"><?php echo $totalDevueltos; ?></p>
            <a href="/objetosdevueltos" class="text-green-500 text-sm hover:underline mt-2 block">Ver historial</a>
        </div>

        <!-- Acción rápida -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
            <h3 class="text-gray-500 text-sm font-medium">Acción rápida</h3>
            <a href="/objetosperdidos/crear" class="inline-block bg-orange-500 text-white px-4 py-2 rounded mt-2 text-sm hover:bg-orange-600">Registrar objeto</a>
        </div>
    </div>

    <!-- Sección de objetos recientes -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Objetos perdidos recientes</h2>
        </div>
        <div class="divide-y divide-gray-200">
            <?php if (empty($objetosRecientes)): ?>
                <p class="p-6 text-gray-500">No hay objetos recientes</p>
            <?php else: ?>
                <?php foreach ($objetosRecientes as $objeto): ?>
                    <div class="p-6 hover:bg-gray-50 transition-colors duration-150">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-medium text-gray-800"><?php echo htmlspecialchars($objeto->nombre); ?></h3>
                                <p class="text-sm text-gray-500 mt-1"><?php echo htmlspecialchars($objeto->descripcion); ?></p>
                                <p class="text-xs text-gray-400 mt-2">
                                    <?php echo date('d/m/Y H:i', strtotime($objeto->fecha_reporte)); ?>
                                </p>
                            </div>
                            <a href="/objetosperdidos/devolver/<?php echo $objeto->id; ?>" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">Devolver</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="px-6 py-4 bg-gray-50 text-right">
            <a href="/objetosperdidos" class="text-blue-500 text-sm hover:underline">Ver todos los objetos</a>
        </div>
    </div>

    <!-- Gráfico de devoluciones -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Devoluciones por mes</h2>
        <div class="relative" style="height: 300px; width: 100%;">
            <!-- Usamos htmlspecialchars solo para escapes básicos -->
            <canvas id="devolucionesChart"
                data-chart='<?php echo json_encode($datosGrafico, JSON_HEX_APOS | JSON_HEX_QUOT); ?>'></canvas>
        </div>
    </div>
</div>