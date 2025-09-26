function abrirDB() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open('GlobalFrutDB', 1);
    request.onupgradeneeded = (e) => {
      const db = e.target.result;
      db.createObjectStore('precios', { keyPath: 'id' });
      db.createObjectStore('estimacionesPendientes', { autoIncrement: true });
      db.createObjectStore('config', { keyPath: 'id' });
    };
    request.onsuccess = (e) => resolve(e.target.result);
    request.onerror = (e) => reject(e.target.error);
  });
}       