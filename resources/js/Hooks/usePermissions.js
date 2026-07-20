import { usePage } from '@inertiajs/react';

export default function usePermissions() {
    const { userRoles, userPermissions } = usePage().props;

    const roles = Array.isArray(userRoles) ? userRoles : [];
    const permissions = Array.isArray(userPermissions) ? userPermissions : [];

    const isSuperAdmin = roles.includes('Super Admin') || roles.includes('Admin');

    const can = (permiso) => isSuperAdmin || !permiso || permissions.includes(permiso);

    return { isSuperAdmin, roles, permissions, can };
}
