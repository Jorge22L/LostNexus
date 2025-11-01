document.addEventListener('DOMContentLoaded', function() {
    const chartCanvas = document.getElementById('devolucionesChart');
    if (!chartCanvas) {
        //console.error('No se encontró el elemento canvas');
        return;
    }

    try {
        // Obtener y validar los datos
        const rawData = chartCanvas.getAttribute('data-chart');
        if (!rawData) {
            throw new Error('No se encontraron datos para el gráfico');
        }

        const chartData = JSON.parse(rawData);
        
        // Validar estructura de datos
        if (!chartData || !chartData.meses || !chartData.totales) {
            throw new Error('Estructura de datos incorrecta');
        }

        //console.log('Datos del gráfico:', chartData); // Para depuración

        new Chart(chartCanvas, {
            type: 'bar',
            data: {
                labels: chartData.meses,
                datasets: [{
                    label: 'Devoluciones',
                    data: chartData.totales,
                    backgroundColor: 'rgba(0, 154, 168, 0.7)',
                    borderColor: 'rgba(0, 154, 168, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: Math.max(...chartData.totales) + 1,
                        ticks: {
                            precision: 0,
                            stepSize: 1
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    } catch (error) {
        console.error('Error al crear el gráfico:', error);
        
        // Mostrar mensaje de error en pantalla (opcional)
        const errorDiv = document.createElement('div');
        errorDiv.className = 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4';
        errorDiv.textContent = 'Error al cargar el gráfico: ' + error.message;
        chartCanvas.parentNode.insertBefore(errorDiv, chartCanvas);
    }
});