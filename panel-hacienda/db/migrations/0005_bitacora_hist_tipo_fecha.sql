-- 0005: Indice por nivel y fecha en BITACORA_HIST para el filtro y orden por Nivel de Auditoria > Logs (con historico).
-- Idempotente. Sin columnas incluidas (las filas se completan por RID; TEXTO es nvarchar(max)). Build offline (Standard), en tempdb.
-- objetos: dbo.BITACORA_HIST.IX_BITACORA_HIST_TIPO_FECHA
IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE object_id = OBJECT_ID('dbo.BITACORA_HIST') AND name = 'IX_BITACORA_HIST_TIPO_FECHA')
    CREATE NONCLUSTERED INDEX IX_BITACORA_HIST_TIPO_FECHA ON dbo.BITACORA_HIST (TIPO ASC, FECHA_HORA DESC)
    WITH (SORT_IN_TEMPDB = ON, MAXDOP = 4);
GO
