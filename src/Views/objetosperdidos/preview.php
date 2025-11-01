<section class="flex items-center justify-center min-h-screen bg-gray-100 py-8">
    <div class="w-full max-w-2xl bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-cyan-500 to-teal-400 p-6">
            <h2 class="text-2xl font-bold text-white">Datos Capturados</h2>
            <p class="text-white/90">Revisa la información del objeto</p>
        </div>
        
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Columna Izquierda -->
                <div class="space-y-4">
                    <div>
                        <h3 class="font-medium text-gray-700">Nombre del Objeto:</h3>
                        <p class="bg-gray-50 p-3 rounded-lg"><?php echo htmlspecialchars($datos['nombre'] ?? ''); ?></p>
                    </div>
                    
                    <div>
                        <h3 class="font-medium text-gray-700">Punto de Recepción:</h3>
                        <p class="bg-gray-50 p-3 rounded-lg">
                            <?php 
                            $puntos = [
                                '1' => 'Recepción Principal',
                                '2' => 'Área de Cafetería',
                                '3' => 'Sala de Conferencias',
                                '4' => 'Estacionamiento'
                            ];
                            echo htmlspecialchars($puntos[$datos['idpunto_recepcion']] ?? '');
                            ?>
                        </p>
                    </div>
                </div>
                
                <!-- Columna Derecha -->
                <div class="space-y-4">
                    <div>
                        <h3 class="font-medium text-gray-700">Descripción:</h3>
                        <p class="bg-gray-50 p-3 rounded-lg min-h-[120px]"><?php echo nl2br(htmlspecialchars($datos['descripcion'] ?? '')); ?></p>
                    </div>
                    
                    <div>
                        <h3 class="font-medium text-gray-700">Tipo de Objeto:</h3>
                        <p class="bg-gray-50 p-3 rounded-lg">
                            <?php 
                            $categorias = [
                                '1' => 'Billetera',
                                '2' => 'Teléfono',
                                '3' => 'Llaves',
                                '4' => 'Otros'
                            ];
                            echo htmlspecialchars($categorias[$datos['idcategoria']] ?? '');
                            ?>
                        </p>
                    </div>
                    
                    <?php if (!empty($datos['observaciones'])): ?>
                    <div>
                        <h3 class="font-medium text-gray-700">Observaciones:</h3>
                        <p class="bg-gray-50 p-3 rounded-lg min-h-[120px]"><?php echo nl2br(htmlspecialchars($datos['observaciones'] ?? '')); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="flex justify-between pt-4">
                <a href="/objetosperdidos/crear" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition">
                    Volver al Formulario
                </a>
                
                <a href="/objetosperdidos" class="bg-gradient-to-r from-cyan-500 to-teal-500 hover:from-cyan-600 hover:to-teal-600 text-white font-bold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transition">
                    Finalizar
                </a>
            </div>
        </div>
    </div>
</section>