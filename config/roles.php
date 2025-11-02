<?php
// Roles (usa los que ya tengas en tu DB)
const ROLE_ADMIN       = 'administrador';
const ROLE_COLABORADOR = 'colaborador';
const ROLE_DONANTE     = 'donante';

// Permisos simples (clave => [roles que pueden])
$PERMISOS = [
  'usuarios.listar'      => [ROLE_ADMIN],
  'usuarios.crear'       => [ROLE_ADMIN],
  'productos.crear'      => [ROLE_ADMIN, ROLE_COLABORADOR],
  'productos.listar'     => [ROLE_ADMIN, ROLE_COLABORADOR, ROLE_DONANTE],
  'donaciones.registrar' => [ROLE_DONANTE, ROLE_COLABORADOR, ROLE_ADMIN],
];