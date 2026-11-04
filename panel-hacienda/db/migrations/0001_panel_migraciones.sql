-- 0001: tabla de control de migraciones del panel-hacienda.
-- (migrar.php tambien la crea antes de leer el estado; aqui queda documentada.)
-- objetos: dbo.PANEL_MIGRACIONES
IF OBJECT_ID('dbo.PANEL_MIGRACIONES', 'U') IS NULL
CREATE TABLE dbo.PANEL_MIGRACIONES (
    NOMBRE      varchar(200)  NOT NULL CONSTRAINT PK_PANEL_MIGRACIONES PRIMARY KEY,   -- nombre del archivo .sql
    SHA256      char(64)      NOT NULL,                                               -- contenido aplicado
    APLICADA    datetime2(0)  NOT NULL CONSTRAINT DF_PANEL_MIGRACIONES_F DEFAULT SYSDATETIME(),
    DURACION_MS int           NULL,
    USUARIO     nvarchar(120) NULL,                                                   -- cli:<usuario so> | panel:<usuario>
    HOST        nvarchar(120) NULL
);
GO
