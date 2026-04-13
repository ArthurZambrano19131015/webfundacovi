import Dexie from 'dexie';

export const db = new Dexie('FundacoviOfflineDB');

db.version(1).stores({
    roles: 'id_local, id_servidor, synced, nombre_rol',
    usuarios: 'id_local, id_servidor, synced, id_rol, email, nombre_completo, telefono, estado_activo'
});

export function generateUUID() {
    return crypto.randomUUID();
}