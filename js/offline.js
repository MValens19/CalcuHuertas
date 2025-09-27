// js/offline.js
function abrirDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('GlobalFrutDB', 1);
        request.onupgradeneeded = event => {
            const db = event.target.result;
            db.createObjectStore('precios', { keyPath: 'id' });
            db.createObjectStore('config', { keyPath: 'id' });
            db.createObjectStore('pendientes', { keyPath: 'id', autoIncrement: true });
        };
        request.onsuccess = event => resolve(event.target.result);
        request.onerror = event => reject(event.target.error);
    });
}

async function guardarPendiente(data) {
    try {
        const db = await abrirDB();
        const tx = db.transaction('pendientes', 'readwrite');
        await tx.objectStore('pendientes').add(data);
    } catch (error) {
        console.error('Error al guardar pendiente:', error);
    }
}

async function sincronizarPendientes() {
    try {
        const db = await abrirDB();
        const tx = db.transaction('pendientes', 'readwrite');
        const store = tx.objectStore('pendientes');
        const pendientes = await store.getAll();
        for (const pendiente of pendientes) {
            try {
                const pendienteData = { ...pendiente };
                const formData = new FormData();
                if (pendiente.foto) {
                    const file = dataURLtoFile(pendiente.foto, 'foto.jpg');
                    formData.append('foto', file);
                    delete pendienteData.foto; // Quita base64 del JSON
                }
                formData.append('data', JSON.stringify(pendienteData));
                const res = await fetch('controller/db_guardar_estimacion.php', {
                    method: 'POST',
                    body: formData
                });
                if (res.ok) {
                    await store.delete(pendiente.id);
                }
            } catch (error) {
                console.error('Error al sincronizar:', error);
            }
        }
    } catch (error) {
        console.error('Error al sincronizar pendientes:', error);
    }
}

// Log to confirm script loaded
console.log('offline.js loaded');

// Función helper para convertir base64 a File (necesaria para sincronizar fotos)
function dataURLtoFile(dataurl, filename) {
    if (!dataurl) return null;
    let arr = dataurl.split(','),
        mime = arr[0].match(/:(.*?);/)[1],
        bstr = atob(arr[1]),
        n = bstr.length,
        u8arr = new Uint8Array(n);
    while (n--) u8arr[n] = bstr.charCodeAt(n);
    return new File([u8arr], filename, { type: mime });
}

// Exportar funciones para usarlas en index.php
export { abrirDB, guardarPendiente, sincronizarPendientes, dataURLtoFile };