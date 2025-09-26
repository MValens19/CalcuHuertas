// Abrir o crear la base de datos IndexedDB
function abrirDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('GlobalFrutDB', 1);
        request.onupgradeneeded = (e) => {
            const db = e.target.result;
            // Crear almacenes
            db.createObjectStore('precios', { keyPath: 'id' });
            db.createObjectStore('config', { keyPath: 'id' });
            db.createObjectStore('estimacionesPendientes', { autoIncrement: true });
        };
        request.onsuccess = (e) => resolve(e.target.result);
        request.onerror = (e) => reject(e.target.error);
    });
}

// Guardar estimación pendiente en IndexedDB
async function guardarPendiente(data) {
    try {
        const db = await abrirDB();
        const tx = db.transaction('estimacionesPendientes', 'readwrite');
        const store = tx.objectStore('estimacionesPendientes');
        await store.add(data);
        return true;
    } catch (error) {
        console.error('Error al guardar en IndexedDB:', error);
        return false;
    }
}

// Sincronizar estimaciones pendientes (usada en index.php, pero definida aquí para modularidad)
async function sincronizarPendientes() {
    try {
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
                await store.delete(data.id); // Eliminar si se sincronizó correctamente
            }
        }
        // Notificación push si está configurada
        if ('serviceWorker' in navigator && 'PushManager' in window) {
            navigator.serviceWorker.controller?.postMessage('sync-complete');
        }
    } catch (error) {
        console.error('Error al sincronizar:', error);
    }
}

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