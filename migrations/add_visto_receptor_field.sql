-- Agregar campo para marcar solicitudes vistas por el receptor
-- Esto permite que las notificaciones desaparezcan cuando el receptor ve sus solicitudes

ALTER TABLE solicitud
ADD COLUMN visto_por_receptor TINYINT(1) NOT NULL DEFAULT 0 AFTER observacion;

-- Marcar todas las solicitudes existentes como vistas para evitar notificaciones antiguas
UPDATE solicitud SET visto_por_receptor = 1 WHERE estado IN ('Aprobada', 'Rechazada');
