    document.getElementById('fechaActual').textContent = new Date().toLocaleDateString('es-MX');

        const contenedor = document.getElementById('formularioDinamico');
        const resultado = document.getElementById('resultadoPromedio');
        const btnGuardar = document.getElementById('btnGuardar');

        // Precios simulados por calibre y mercado
        const preciosSimulados = {
            usa: {
                '32s': 12,
                '36s': 11.5,
                '40s': 11,
                '48s': 10.5,
                '60s': 10,
                '70s': 9.5,
                '84s': 9,
                'Extra': 8,
                'Primera': 7.5,
                'Canica': 7,
                'Comercial': 6.5,
                'Primera B': 6,
                'Desecho': 5
            },
            asia: {
                '16s': 14,
                '20s': 13.5,
                '24s': 13,
                '30s': 12.5,
                '35s': 12,
                '12s': 15,
                '16s_canada': 14.5,
                '18s': 14,
                '20s_canada': 13.5,
                '22s': 13,
                '24s_canada': 12.5,
                'Extra': 8,
                'Primera': 7.5,
                'Canica': 7,
                'Comercial': 6.5,
                'Primera B': 6,
                'Desecho': 5
            }
        };

        // Cambiar formulario dinámico según selección
        document.getElementById('exportacion').addEventListener('change', function() {
            resultado.textContent = '';
            btnGuardar.disabled = true;
            btnGuardar.classList.add('opacity-50', 'cursor-not-allowed');

            const valor = this.value;
            contenedor.innerHTML = '';

            if (valor === 'usa') {
                contenedor.innerHTML = createSection('Mercado USA', ['32s', '36s', '40s', '48s', '60s', '70s', '84s']);
                contenedor.innerHTML += createSection('Mercado Nacional', ['Extra', 'Primera', 'Canica', 'Comercial', 'Primera B', 'Desecho']);
                contenedor.innerHTML += `<textarea placeholder="Observaciones" class="w-full px-3 py-2 rounded-lg bg-gray-800 border border-gray-600 text-white mt-4"></textarea>`;
            } else if (valor === 'asia') {
                contenedor.innerHTML = createSection('Mercado Japón', ['16s', '20s', '24s', '30s', '35s']);
                contenedor.innerHTML += createSection('Mercado Canadá', ['12s', '16s', '18s', '20s', '22s', '24s']);
                contenedor.innerHTML += createSection('Mercado Nacional', ['Extra', 'Primera', 'Canica', 'Comercial', 'Primera B', 'Desecho']);
                contenedor.innerHTML += `<textarea placeholder="Observaciones" class="w-full px-3 py-2 rounded-lg bg-gray-800 border border-gray-600 text-white mt-4"></textarea>`;
            }

            // Añadir evento input a los nuevos inputs para calcular automáticamente
            document.querySelectorAll('.input-porcentaje').forEach(input => {
                input.addEventListener('input', calcularPromedio);
            });
        });

        // Función para crear sección con etiquetas y inputs con % a la derecha
        function createSection(titulo, calibres) {
            let html = `<h2 class="text-md font-semibold text-green-400 mt-4">${titulo}</h2>`;
            calibres.forEach(c => {
                html += `
        <div class="flex items-center mt-2 space-x-2">
          <label class="w-24">${c.toUpperCase()}</label>
          <input type="number" min="0" max="100" step="0.01" placeholder="%" class="input-porcentaje flex-grow px-3 py-2 rounded-l-lg bg-gray-800 border border-gray-600 text-white" data-calibre="${c}" />
          <span class="bg-gray-700 text-white px-3 py-2 rounded-r-lg select-none">%</span>
        </div>
        `;
            });
            return html;
        }

        function calcularPromedio() {
            const exportacion = document.getElementById('exportacion').value;
            let totalPorcentaje = 0;
            let promedio = 0;

            const inputs = Array.from(document.querySelectorAll('.input-porcentaje'));

            inputs.forEach(input => {
                const val = parseFloat(input.value);
                if (!isNaN(val) && val > 0) {
                    totalPorcentaje += val;

                    const calibre = input.dataset.calibre;
                    let precio = 0;

                    if (exportacion === 'usa') {
                        precio = preciosSimulados.usa[calibre] ?? 0;
                    } else if (exportacion === 'asia') {
                        if (preciosSimulados.asia[calibre] !== undefined) {
                            precio = preciosSimulados.asia[calibre];
                        } else if (calibre === '16s') {
                            precio = preciosSimulados.asia['16s_canada'] || 0;
                        } else if (calibre === '20s') {
                            precio = preciosSimulados.asia['20s_canada'] || 0;
                        }
                    }

                    promedio += (val * precio) / 100;
                }
            });

            if (totalPorcentaje.toFixed(2) !== '100.00') {
                resultado.classList.remove('text-green-500');
                resultado.classList.add('text-red-500');
                resultado.textContent = `❌ La suma de porcentajes es ${totalPorcentaje.toFixed(2)}%. Debe ser 100%.`;
                btnGuardar.disabled = true;
                btnGuardar.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                resultado.classList.remove('text-red-500');
                resultado.classList.add('text-green-500');
                resultado.textContent = `Promedio estimado: $${promedio.toFixed(2)}`;
                btnGuardar.disabled = false;
                btnGuardar.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        // Inicializar fecha
        document.getElementById('fechaActual').textContent = new Date().toLocaleDateString('es-MX');

        btnGuardar.addEventListener('click', () => {
            alert('Estimación guardada correctamente.');
            // Aquí puedes agregar la lógica real para enviar datos al backend
        });