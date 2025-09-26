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

    <!-- ESTADO DE RED -->
    <span id="estadoRed" class="text-sm text-gray-300 mb-2">Cargando estado...</span>

    <!-- MENÚ DE NAVEGACIÓN -->
    <nav class="w-full bg-gray-800 p-2 flex justify-around mb-4">
        <button id="btnEstimacion" class="text-white font-semibold">Nueva Estimación</button>
        <button id="btnHistorial" class="text-white">Historial</button>
        <button id="btnReportes" class="text-white">Reportes</button>
    </nav>

    <!-- SECCIÓN DE ESTIMACIÓN (principal, visible por defecto) -->
    <div id="seccionEstimacion" class="w-full">

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

            <!-- SECCIÓN PARA FOTOS -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-300">Adjuntar foto (opcional)</label>
                <input type="file" accept="image/*" capture="camera" id="inputFoto" class="w-full px-3 py-2 rounded-lg bg-gray-800 border border-gray-600 text-white" />
                <img id="vistaPrevia" class="mt-2 w-full h-auto rounded-lg hidden" alt="Vista previa de la foto" />
            </div>

        </form>

        <!-- CONTENEDOR PARA FORMULARIOS DINÁMICOS -->
        <div id="formularioDinamico" class="w-full mt-6 space-y-4"></div>

        <!-- RESULTADO PROMEDIO -->
        <div id="resultadoPromedio" class="w-full mt-4 text-center font-semibold text-lg"></div>

        <!-- BOTÓN GUARDAR -->
        <button id="btnGuardar" disabled class="w-full bg-blue-700 hover:bg-blue-800 text-white py-2 rounded-lg mt-4 opacity-50 cursor-not-allowed">Guardar estimación</button>

        <!-- Mensaje de confirmación -->
        <div id="mensajeConfirmacion" class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50 hidden">
            <div class="bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2 animate-bounce transition-all duration-500">
                <svg class="w-6 h-6 text-white animate-pulse" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                <span id="textoConfirmacion">¡Estimación guardada correctamente!</span>
            </div>
        </div>

    </div>

    <!-- SECCIÓN DE HISTORIAL (oculta por defecto) -->
    <div id="historial" class="w-full mt-4 hidden">
        <h2 class="text-md font-semibold text-green-400">Historial de Estimaciones</h2>
        <table class="w-full bg-gray-800 rounded-lg">
            <thead>
                <tr>
                    <th class="p-2">Fecha</th>
                    <th class="p-2">Productor</th>
                    <th class="p-2">Promedio</th>
                    <th class="p-2">Ubicación</th>
                </tr>
            </thead>
            <tbody id="tablaHistorial"></tbody>
        </table>
    </div>

    <!-- SECCIÓN DE REPORTES (oculta por defecto) -->
    <div id="reportes" class="w-full mt-4 hidden">
        <h2 class="text-md font-semibold text-green-400">Reportes</h2>
        <input type="date" id="fechaInicio" class="w-full px-3 py-2 rounded-lg bg-gray-800 border border-gray-600 text-white" />
        <input type="date" id="fechaFin" class="w-full px-3 py-2 rounded-lg bg-gray-800 border border-gray-600 text-white mt-2" />
        <button id="btnGenerarReporte" class="w-full bg-blue-700 hover:bg-blue-800 text-white py-2 rounded-lg mt-2">Generar Reporte</button>
        <canvas id="chartPromedios" class="w-full h-64 bg-gray-700 rounded-lg mt-4"></canvas>
    </div>

    <script src="js/buscar_huertas.js"></script>
    <script src="js/buscar_municipio.js"></script>
    <script src="js/offline.js"></script> <!-- Nuevo script para IndexedDB y offline -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Para gráficos en reportes -->

    <script>
        // Inicializar fecha
        document.getElementById('fechaActual').textContent = new Date().toLocaleDateString('es-MX');

        const contenedor = document.getElementById('formularioDinamico');
        const resultado = document.getElementById('resultadoPromedio');
        const btnGuardar = document.getElementById('btnGuardar');
        const inputFoto = document.getElementById('inputFoto');
        const vistaPrevia = document.getElementById('vistaPrevia');
        const estadoRed = document.getElementById('estadoRed');

        // Precios simulados por calibre y mercado
        let preciosSimulados = {};

        // Cargar precios con soporte offline
        async function cargarPrecios() {
            try {
                if (navigator.onLine) {
                    const response = await fetch('controller/db_cargar_precios.php');
                    preciosSimulados = await response.json();
                    const db = await abrirDB();
                    const tx = db.transaction('precios', 'readwrite');
                    tx.objectStore('precios').put({ id: 'precios', data: preciosSimulados });
                    console.log('Precios cargados y guardados:', preciosSimulados);
                } else {
                    const db = await abrirDB();
                    const tx = db.transaction('precios', 'readonly');
                    const precios = await tx.objectStore('precios').get('precios');
                    preciosSimulados = precios?.data || {};
                    console.log('Precios cargados offline:', preciosSimulados);
                }
            } catch (error) {
                console.error('Error al cargar precios:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', async () => {
            await cargarPrecios();
            await cargarGramajesTipos();
            // Actualizar estado de red
            estadoRed.textContent = navigator.onLine ? 'Online' : 'Offline';
        });

        // Vista previa de foto
        inputFoto.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    vistaPrevia.src = event.target.result;
                    vistaPrevia.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
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

        // Guardar estimación con soporte offline y geolocalización
        btnGuardar.addEventListener('click', async () => {
            const form = document.getElementById('formularioPrevio');
            const exportacion = document.getElementById('exportacion').value;
            const promedioText = resultado.textContent;
            const promedio = parseFloat(promedioText.split('$')[1]) || 0;

            if (!form.checkValidity()) {
                alert('Por favor, completa todos los campos requeridos.');
                return;
            }

            // Obtener ubicación actual
            let latitud = null;
            let longitud = null;
            try {
                const position = await new Promise((resolve, reject) => {
                    navigator.geolocation.getCurrentPosition(resolve, reject, { enableHighAccuracy: true, timeout: 10000 });
                });
                latitud = position.coords.latitude;
                longitud = position.coords.longitude;
            } catch (error) {
                console.error('Error al obtener ubicación:', error);
                alert('No se pudo obtener la ubicación. Asegúrate de permitir el acceso a la geolocalización.');
                return; // Opcional: continuar sin ubicación si se desea
            }

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
                foto: vistaPrevia.src || '', // Base64 de la foto
                latitud,
                longitud,
                detalles: {}
            };

            document.querySelectorAll('.input-porcentaje').forEach(input => {
                const calibre = input.dataset.calibre;
                const val = parseFloat(input.value) || 0;
                data.detalles[calibre] = val;
            });

            try {
                if (navigator.onLine) {
                    const formData = new FormData();
                    formData.append('data', JSON.stringify(data));
                    if (inputFoto.files[0]) {
                        formData.append('foto', inputFoto.files[0]);
                    }

                    const res = await fetch('controller/db_guardar_estimacion.php', {
                        method: 'POST',
                        body: formData
                    });

                    if (res.ok) {
                        mostrarConfirmacion('¡Estimación guardada correctamente!');
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        alert('❌ Error al guardar la estimación.');
                    }
                } else {
                    await guardarPendiente(data);
                    mostrarConfirmacion('Estimación guardada localmente. Se enviará cuando haya conexión.');
                }
            } catch (error) {
                console.error('Error al guardar:', error);
                alert('❌ Error de conexión con el servidor.');
            }
        });

        function mostrarConfirmacion(texto) {
            const mensaje = document.getElementById('mensajeConfirmacion');
            document.getElementById('textoConfirmacion').textContent = texto;
            mensaje.classList.remove('hidden');
            setTimeout(() => mensaje.classList.add('hidden'), 3000);
        }

        // Cargar gramajes y tipos con soporte offline (similar a cargarPrecios)
        async function cargarGramajesTipos() {
            try {
                let data;
                if (navigator.onLine) {
                    const res = await fetch('controller/db_gramaje_tipo.php');
                    data = await res.json();
                    const db = await abrirDB();
                    const tx = db.transaction('config', 'readwrite');
                    tx.objectStore('config').put({ id: 'gramajesTipos', data });
                } else {
                    const db = await abrirDB();
                    const tx = db.transaction('config', 'readonly');
                    data = (await tx.objectStore('config').get('gramajesTipos'))?.data || { gramajes: [], tipos: [] };
                }

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

        // Sincronización al reconectar
        window.addEventListener('online', async () => {
            estadoRed.textContent = 'Online';
            const db = await abrirDB();
            const tx = db.transaction('estimacionesPendientes', 'readwrite');
            const store = tx.objectStore('estimacionesPendientes');
            const pendientes = await store.getAll();
            for (const data of pendientes) {
                const formData = new FormData();
                formData.append('data', JSON.stringify(data));
                if (data.foto) {
                    formData.append('foto', dataURLtoFile(data.foto, 'foto.jpg'));
                }
                const res = await fetch('controller/db_guardar_estimacion.php', {
                    method: 'POST',
                    body: formData
                });
                if (res.ok) {
                    await store.delete(data.id); // Asume que tienes un id autoIncrement
                }
            }
            // Notificación push si configurada
            if ('serviceWorker' in navigator && 'PushManager' in window) {
                navigator.serviceWorker.controller.postMessage('sync-complete');
            }
        });

        window.addEventListener('offline', () => {
            estadoRed.textContent = 'Offline';
        });

        // Función helper para convertir base64 a File
        function dataURLtoFile(dataurl, filename) {
            if (!dataurl) return null;
            let arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
                bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
            while (n--) u8arr[n] = bstr.charCodeAt(n);
            return new File([u8arr], filename, { type: mime });
        }

        // Navegación entre secciones
        const seccionEstimacion = document.getElementById('seccionEstimacion');
        const seccionHistorial = document.getElementById('historial');
        const seccionReportes = document.getElementById('reportes');

        document.getElementById('btnEstimacion').addEventListener('click', () => {
            seccionEstimacion.classList.remove('hidden');
            seccionHistorial.classList.add('hidden');
            seccionReportes.classList.add('hidden');
        });

        document.getElementById('btnHistorial').addEventListener('click', async () => {
            seccionEstimacion.classList.add('hidden');
            seccionHistorial.classList.remove('hidden');
            seccionReportes.classList.add('hidden');
            await cargarHistorial();
        });

        document.getElementById('btnReportes').addEventListener('click', () => {
            seccionEstimacion.classList.add('hidden');
            seccionHistorial.classList.add('hidden');
            seccionReportes.classList.remove('hidden');
        });

        // Cargar historial con soporte offline
        async function cargarHistorial() {
            const tbody = document.getElementById('tablaHistorial');
            tbody.innerHTML = '';
            let estimaciones = [];
            if (navigator.onLine) {
                const res = await fetch('controller/db_obtener_estimaciones.php');
                estimaciones = await res.json();
                // Guardar en IndexedDB si quieres cachear historial
            } else {
                const db = await abrirDB();
                estimaciones = await db.transaction('estimacionesPendientes', 'readonly').objectStore('estimacionesPendientes').getAll();
            }
            estimaciones.forEach(e => {
                const ubicacion = e.latitud && e.longitud ? `${e.latitud}, ${e.longitud}` : 'No disponible';
                tbody.innerHTML += `<tr><td class="p-2">${e.fecha}</td><td class="p-2">${e.productor}</td><td class="p-2">$${e.promedio_estimado}</td><td class="p-2">${ubicacion}</td></tr>`;
            });
        }

        // Generar reporte
        document.getElementById('btnGenerarReporte').addEventListener('click', async () => {
            const inicio = document.getElementById('fechaInicio').value;
            const fin = document.getElementById('fechaFin').value;
            if (!inicio || !fin) {
                alert('Selecciona fechas de inicio y fin.');
                return;
            }
            let datos = [];
            if (navigator.onLine) {
                const res = await fetch(`controller/db_reporte_estimaciones.php?inicio=${inicio}&fin=${fin}`);
                datos = await res.json();
            } else {
                alert('Reportes requieren conexión a internet.');
                return;
            }
            // Ejemplo de gráfico con Chart.js (ajusta según datos)
            const ctx = document.getElementById('chartPromedios').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: datos.map(d => d.fecha),
                    datasets: [{
                        label: 'Promedio Estimado',
                        data: datos.map(d => d.promedio_estimado),
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        });

        // Service Worker
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