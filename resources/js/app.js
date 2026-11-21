import initSqlJs from 'sql.js';

const sqlWasmUrl = new URL('/vendor/sql-wasm-browser.wasm', window.location.origin).href;
window.RadixSQL = initSqlJs({ locateFile: () => sqlWasmUrl }).catch((error) => {
    console.error('SQL.js gagal dimuat:', error);
    return null;
});

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/service-worker.js').catch(() => {
            // Offline enhancement must not block application use.
        });
    });
}

