# TODO: Arreglar Registro en DonAppétit

## Problema
El registro no envía datos a la base de datos. Análisis:
- URL del formulario apunta a `/donapetit3-main/app/view/?action=register_post`, pero el router está en `public/index.php`.
- Errores en `AuthController.php`: constructor mal escrito, constantes no definidas, condición incompleta.
- Después de insertar en `usuarios`, no se inserta en `donante` o `receptor` según el rol.

## Pasos a Completar
- [x] Corregir errores en `app/controllers/AuthController.php`
- [x] Cambiar action del formulario en `app/view/auth/register.php` a `/donapetit3-main/public/?controller=Auth&action=registrar`
- [x] Reemplazar 'password' por 'contrasena' en todo el código
- [x] Usar columna 'contrasena' en SQL para coincidir con el esquema de BD actual
- [x] Probar registro básico (insertar en usuarios) - FUNCIONA
- [ ] Implementar inserción en donante/receptor según rol (si es necesario)
- [ ] Verificar conexión a BD y permisos
