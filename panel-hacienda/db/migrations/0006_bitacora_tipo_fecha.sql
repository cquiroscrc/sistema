-- 0006: Indice por nivel y fecha en BITACORA (filtro y orden por Nivel de Auditoria > Logs).
-- Idempotente. Build offline (Standard): bloquea escrituras en BITACORA unos segundos (autorizado con oficinas cerradas).
-- objetos: dbo.BITACORA.IX_BITACORA_TIPO_FECHA
IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE object_id = OBJECT_ID('dbo.BITACORA') AND name = 'IX_BITACORA_TIPO_FECHA')
    CREATE NONCLUSTERED INDEX IX_BITACORA_TIPO_FECHA ON dbo.BITACORA (TIPO ASC, FECHA_HORA DESC)
    WITH (SORT_IN_TEMPDB = ON, MAXDOP = 4);
GO
