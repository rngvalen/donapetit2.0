<?php
// Roles (usa los que ya tengas en tu DB)
const ROLE_ADMIN       = 'administrador';
const ROLE_COLABORADOR = 'colaborador';
const ROLE_DONANTE     = 'donante';
const ROLE_RECEPTOR    = 'receptor';

// Permisos simples (clave => [roles que pueden])
$PERMISOS = [
  'usuarios.listar'      => [ROLE_ADMIN],
  'usuarios.crear'       => [ROLE_ADMIN],
  'productos.crear'      => [ROLE_ADMIN, ROLE_COLABORADOR],
  'productos.listar'     => [ROLE_ADMIN, ROLE_COLABORADOR, ROLE_DONANTE, ROLE_RECEPTOR],
  'donaciones.registrar' => [ROLE_DONANTE, ROLE_COLABORADOR, ROLE_ADMIN],
  'solicitudes.gestionar' => [ROLE_DONANTE],
  'solicitudes.solicitar' => [ROLE_RECEPTOR],
];
