import Dexie from 'dexie';

export const db = new Dexie('FundacoviOfflineDB');

db.version(1).stores({
    roles: 'id_local, id_servidor, synced, nombre_rol',
    usuarios: 'id_local, id_servidor, synced, id_rol, email, nombre_completo, telefono, foto, estado_activo',
    apiarios: 'id_local, id_servidor, synced, id_apicultor, nombre, estado_activo',
    colmenas: 'id_local, id_servidor, synced, id_apiario, identificador, estado_activo',
    estandares: 'id_local, id_servidor, synced, parametro, estado_activo',
    cosechas: 'id_local, id_servidor, synced, id_colmena, fecha_recoleccion',
    lotes: 'id_local, id_servidor, synced, codigo_lote, estado_aprobacion',
    resultados: 'id_local, id_servidor, synced, id_lote, id_estandar',
});

export function generateUUID() {
    return crypto.randomUUID();
}

window.db = db;
window.generateUUID = generateUUID;