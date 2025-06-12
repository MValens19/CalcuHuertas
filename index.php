<!DOCTYPE html>
<html lang="es" class="dark">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Estimación de Precio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        };
    </script>
    <link rel="manifest" href="manifest.json" />
    <meta name="theme-color" content="#0f172a" />
    <link rel="icon" href="icons/Global_Frut.png" />
</head>

<body class="bg-gray-900 text-white min-h-screen flex flex-col items-center p-4">

    <!-- LOGO -->
    <img src="icons/logoglobal.png" alt="Logo Global Frut" class="w-50 h-20 mb-2 bg-gray-700 rounded-lg" />

    <!-- TÍTULO -->
    <h1 class="text-lg font-semibold text-center mb-2">Estimación de Precio Promedio</h1>

    <!-- FECHA ACTUAL -->
    <p class="text-sm text-gray-300 mb-4 text-center">Fecha: <span id="fechaActual"></span></p>

    <!-- FORMULARIO PREVIO -->
    <form id="formularioPrevio" class="w-full space-y-3">
        <input type="text" placeholder="Nombre del productor" class="w-full px-3 py-2 rounded-lg bg-gray-800 border border-gray-600 text-white" required />

        <!-- HUERTAS -->
        <input list="sugerenciasHuerta" id="inputHuerta" name="nombre_huerta"
            class="w-full px-3 py-2 rounded-lg bg-gray-800 border border-gray-600 text-white"
            placeholder="Nombre Huerta...">
        <datalist id="sugerenciasHuerta"></datalist>

        <input type="text" placeholder="Estimación de cosecha " class="w-full px-3 py-2 rounded-lg bg-gray-800 border border-gray-600 text-white" />

        <!-- MUNICIPIO -->
        <select id="selectMunicipio" name="municipio" class="w-full px-3 py-2 rounded-lg bg-gray-800 border border-gray-600 text-white">
            <option value="">Seleccione un municipio</option>
        </select>

        <!-- TIPO CORTE -->
        <div class="flex flex-col gap-3 sm:flex-row">
            <select id="selectGramaje" name="gramaje" class="w-full px-3 py-2 rounded-lg bg-gray-800 border border-gray-600 text-white">
                <option value="">Seleccione un gramaje</option>
            </select>

            <select id="selectTipoCorte" name="tipo_corte" class="w-full px-3 py-2 rounded-lg bg-gray-800 border border-gray-600 text-white">
                <option value="">Seleccione tipo de corte</option>
            </select>
        </div>


        <!-- JEFE DE ACOPIO -->

        <select id="selectJefe" name="jefe_acopio" class="w-full px-3 py-2 rounded-lg bg-gray-800 border border-gray-600 text-white">
            <option value="">Seleccione un jefe</option>
        </select>

        <!-- DESTINO DE EXPORTACIÓN -->
        <label for="exportacion" class="block text-sm font-medium text-gray-300">Destino de exportación</label>
        <select id="exportacion" class="w-full px-3 py-2 rounded-lg bg-gray-800 border border-gray-600 text-white">
            <option value="">Seleccione una opción</option>
            <option value="usa">USA </option>
            <option value="asia">Japón / Canadá </option>
        </select>

    </form>

    <!-- CONTENEDOR PARA FORMULARIOS DINÁMICOS -->
    <div id="formularioDinamico" class="w-full mt-6 space-y-4"></div>

    <!-- RESULTADO PROMEDIO -->
    <div id="resultadoPromedio" class="w-full mt-4 text-center font-semibold text-lg"></div>

    <!-- BOTÓN GUARDAR -->
    <button id="btnGuardar" disabled class="w-full bg-blue-700 hover:bg-blue-800 text-white py-2 rounded-lg mt-4 opacity-50 cursor-not-allowed">Guardar estimación</button>

    <script src="js/buscar_huertas.js">
    </script>

    <script>
        document.getElementById('fechaActual').textContent = new Date().toLocaleDateString('es-MX');

        const contenedor = document.getElementById('formularioDinamico');
        const resultado = document.getElementById('resultadoPromedio');
        const btnGuardar = document.getElementById('btnGuardar');

        // Precios simulados por calibre y mercado
        let preciosSimulados = {};

        async function cargarPrecios() {
            try {
                const response = await fetch('controller/db_cargar_precios.php');
                preciosSimulados = await response.json();
                console.log('Precios cargados:', preciosSimulados);
            } catch (error) {
                console.error('Error al cargar precios:', error);
            }
        }
        document.addEventListener('DOMContentLoaded', () => {
            cargarPrecios();
        });

        // Cambiar formulario dinámico según selección
        document.getElementById('exportacion').addEventListener('change', function() {
            resultado.textContent = '';
            btnGuardar.disabled = true;
            btnGuardar.classList.add('opacity-50', 'cursor-not-allowed');

            const valor = this.value;
            contenedor.innerHTML = '';

            if (valor === 'usa') {
                contenedor.innerHTML = createSection('Mercado USA (Cat 1)', ['32s', '36s', '40s', '48s', '60s', '70s', '84s']);
                contenedor.innerHTML += createSection('Mercado USA (Cat 2)', ['32s2', '36s2', '40s2', '48s2', '60s2', '70s2', '84s2']);
                contenedor.innerHTML += createSection('Mercado Nacional', ['Extra', 'Primera', 'Canica', 'Comercial', 'Primera B', 'Desecho']);
                contenedor.innerHTML += `<textarea placeholder="Observaciones" class="w-full px-3 py-2 rounded-lg bg-gray-800 border border-gray-600 text-white mt-4"></textarea>`;
            } else if (valor === 'asia') {
                contenedor.innerHTML = createSection('Mercado Japón', ['16s', '20s', '24s', '30s', '35s']);
                contenedor.innerHTML += createSection('Mercado Canadá', ['12s_canada', '14s_canada', '16s/18s_canada', '20s/22s_canada', '20s_canada', '22s_canada', '24s_canada']);
                contenedor.innerHTML += createSection('Mercado Nacional', ['extra', 'primera', 'primera b', 'comercial', 'canica', 'desecho']);
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

        btnGuardar.addEventListener('click', async () => {
            const form = document.getElementById('formularioPrevio');
            const exportacion = document.getElementById('exportacion').value;
            const promedioText = resultado.textContent;
            const promedio = parseFloat(promedioText.split('$')[1]) || 0;

            const data = {
                fecha: new Date().toISOString().split('T')[0],
                productor: form[0].value,
                huerta: form[1].value,
                estimacion_cosecha: form[2].value,
                municipio: form[3].value,
                gramaje: form[4].value,
                tipo_corte: form[5].value,
                jefe_acopio: form[6].value,
                exportacion,
                observaciones: document.querySelector('textarea')?.value || '',
                promedio_estimado: promedio,
                detalles: {}
            };

            document.querySelectorAll('.input-porcentaje').forEach(input => {
                const calibre = input.dataset.calibre;
                const val = parseFloat(input.value) || 0;
                data.detalles[calibre] = val;
            });

            try {
                const res = await fetch('controller/db_guardar_estimacion.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (res.ok) {
                    alert('✅ Estimación guardada correctamente.');
                    location.reload();
                } else {
                    alert('❌ Error al guardar la estimación.');
                }
            } catch (error) {
                console.error('Error al guardar:', error);
                alert('❌ Error de conexión con el servidor.');
            }
        });
    </script>

    <script src="js/buscar_municipio.js">
    </script>

    <script>
        async function cargarGramajesTipos() {
            try {
                const res = await fetch('controller/db_gramaje_tipo.php');
                const data = await res.json();

                const selectGramaje = document.getElementById('selectGramaje');
                const selectTipoCorte = document.getElementById('selectTipoCorte');

                data.gramajes.forEach(g => {
                    const option = document.createElement('option');
                    option.value = g;
                    option.textContent = g;
                    selectGramaje.appendChild(option);
                });

                data.tipos.forEach(t => {
                    const option = document.createElement('option');
                    option.value = t;
                    option.textContent = t;
                    selectTipoCorte.appendChild(option);
                });
            } catch (error) {
                console.error('Error al cargar gramajes y tipos de corte:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', cargarGramajesTipos);
    </script>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('sw.js')
                    .then(reg => console.log('Service Worker registrado:', reg.scope))
                    .catch(err => console.log('Error registrando Service Worker:', err));
            });
        }
    </script>

</body>

</html>