  document.getElementById('inputHuerta').addEventListener('input', function() {
            const query = this.value;
            if (query.length >= 2) {
                fetch(`controller/db_buscar_huertas.php?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        const lista = document.getElementById('sugerenciasHuerta');
                        lista.innerHTML = '';
                        data.forEach(nombre => {
                            const option = document.createElement('option');
                            option.value = nombre;
                            lista.appendChild(option);
                        });
                    });
            }
        });