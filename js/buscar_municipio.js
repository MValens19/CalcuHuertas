document.addEventListener('DOMContentLoaded', () => {
            fetch('controller/db_formulario.php')
                .then(response => response.json())
                .then(data => {
                    const selectMunicipio = document.getElementById('selectMunicipio');
                    const selectJefe = document.getElementById('selectJefe');

                    // Llenar municipios
                    data.municipios.forEach(muni => {
                        const option = document.createElement('option');
                        option.value = muni;
                        option.textContent = muni;
                        selectMunicipio.appendChild(option);
                    });

                    // Llenar jefes
                    data.jefes.forEach(jefe => {
                        const option = document.createElement('option');
                        option.value = jefe.id;
                        option.textContent = jefe.nombre;
                        selectJefe.appendChild(option);
                    });
                })
                .catch(error => console.error('Error al cargar selects:', error));
        });