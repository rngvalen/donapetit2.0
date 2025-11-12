-- Sistema de reservas con expiración automática
-- Permite que el donante reserve stock por 2 horas y luego confirme si el receptor retiró

-- 1. Agregar campos para gestión de retiros
ALTER TABLE solicitud
ADD COLUMN fecha_limite_retiro DATETIME NULL AFTER estado,
ADD COLUMN retiro_confirmado TINYINT(1) NOT NULL DEFAULT 0 AFTER fecha_limite_retiro,
ADD COLUMN fecha_retiro DATETIME NULL AFTER retiro_confirmado;

-- 2. Agregar campo para stock reservado en productos_donante
ALTER TABLE productos_donante
ADD COLUMN cantidad_reservada INT NOT NULL DEFAULT 0 AFTER cantidad_disponible;

-- Comentarios:
-- fecha_limite_retiro: Fecha límite para que el receptor retire (2 horas después de aprobar)
-- retiro_confirmado: 0 = pendiente, 1 = confirmado que el receptor retiró
-- fecha_retiro: Fecha en que se confirmó el retiro
-- cantidad_reservada: Cantidad temporalmente reservada (no disponible pero no donada aún)
