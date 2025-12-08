// Sistema de filtros dinámicos para visualizador de partidas (solo Admin/Mod)

document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const partidasRows = document.querySelectorAll('.partida-row');
    
    // Event listener para cada botón de filtro
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Actualizar estado visual de los botones
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filtrar las filas de la tabla
            partidasRows.forEach(row => {
                const estadoPartida = row.getAttribute('data-estado');
                
                if (filter === 'all') {
                    // Mostrar todas las partidas
                    row.style.display = '';
                } else if (estadoPartida === filter) {
                    // Mostrar solo las que coinciden con el filtro
                    row.style.display = '';
                } else {
                    // Ocultar las que no coinciden
                    row.style.display = 'none';
                }
            });
            
            // Actualizar contador de partidas visibles
            actualizarContador(filter);
        });
    });
    
    // Función para actualizar el contador (opcional)
    function actualizarContador(filter) {
        const visibles = document.querySelectorAll('.partida-row:not([style*="display: none"])').length;
        const total = partidasRows.length;
        
        console.log(`Mostrando ${visibles} de ${total} partidas (filtro: ${filter})`);
    }
});

// Estilos CSS inline para los botones de filtro
const style = document.createElement('style');
style.textContent = `
    .filter-btn {
        padding: 8px 16px;
        margin: 0 5px;
        border: 2px solid #333;
        background-color: #fff;
        color: #333;
        cursor: pointer;
        border-radius: 5px;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    
    .filter-btn:hover {
        background-color: #f0f0f0;
        transform: translateY(-2px);
    }
    
    .filter-btn.active {
        background-color: #333;
        color: #fff;
        font-weight: bold;
    }
    
    /* Estilos opcionales para destacar estados */
    .estado-nueva td:first-child {
        border-left: 4px solid #4CAF50;
    }
    
    .estado-activa td:first-child {
        border-left: 4px solid #2196F3;
    }
    
    .estado-pausada td:first-child {
        border-left: 4px solid #FF9800;
    }
    
    .estado-finalizada td:first-child {
        border-left: 4px solid #9E9E9E;
    }
    
    .estado-cancelada td:first-child {
        border-left: 4px solid #F44336;
    }
`;
document.head.appendChild(style);