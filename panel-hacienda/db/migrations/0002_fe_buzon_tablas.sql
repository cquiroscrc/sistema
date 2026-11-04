-- 0002: Buzon FE (panel-hacienda): correos/documentos recibidos en facturaelectronica@lospaticas.com.
-- Idempotente (solo crea lo que falta; nunca borra datos). Montos con los mismos nombres que dbo.DOC_ELEC_RECIBIDO.
-- objetos: dbo.FE_BUZON_CORREOS, dbo.FE_BUZON_DOCUMENTOS, dbo.FE_BUZON_ARCHIVOS, dbo.FE_BUZON_CORREOS.IX_FE_BUZON_CORREOS_MSGID, dbo.FE_BUZON_DOCUMENTOS.IX_FE_BUZON_DOC_FECHA, dbo.FE_BUZON_DOCUMENTOS.IX_FE_BUZON_DOC_EMISOR, dbo.FE_BUZON_ARCHIVOS.IX_FE_BUZON_ARCH_CLAVE, dbo.FE_BUZON_ARCHIVOS.IX_FE_BUZON_ARCH_CORREO
IF OBJECT_ID('dbo.FE_BUZON_CORREOS', 'U') IS NULL
CREATE TABLE dbo.FE_BUZON_CORREOS (
    ID              int IDENTITY(1,1) NOT NULL CONSTRAINT PK_FE_BUZON_CORREOS PRIMARY KEY,
    CUENTA          varchar(120)   NOT NULL,
    CARPETA         nvarchar(200)  NOT NULL,
    UIDVALIDITY     bigint         NOT NULL,
    UID             bigint         NOT NULL,
    MESSAGE_ID      varchar(400)   NULL,
    REMITENTE       nvarchar(400)  NULL,
    REMITENTE_EMAIL varchar(200)   NULL,
    PARA            nvarchar(400)  NULL,
    ASUNTO          nvarchar(1000) NULL,
    FECHA_CORREO    datetime2(0)   NULL,
    FECHA_RECIBIDO  datetime2(0)   NULL,
    TAMANO          int            NULL,
    ADJUNTOS        int            NOT NULL CONSTRAINT DF_FE_BUZON_CORREOS_ADJ DEFAULT 0,
    DOCUMENTOS      int            NOT NULL CONSTRAINT DF_FE_BUZON_CORREOS_DOCS DEFAULT 0,
    ESTADO          varchar(20)    NOT NULL,   -- ok | sin_documentos | propio | duplicado | muy_grande | error
    ERROR           nvarchar(2000) NULL,
    FECHA_PROCESADO datetime2(0)   NOT NULL CONSTRAINT DF_FE_BUZON_CORREOS_FP DEFAULT SYSDATETIME(),
    CONSTRAINT UQ_FE_BUZON_CORREOS_UID UNIQUE (CUENTA, CARPETA, UIDVALIDITY, UID)
);
GO
IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE object_id = OBJECT_ID('dbo.FE_BUZON_CORREOS') AND name = 'IX_FE_BUZON_CORREOS_MSGID')
    CREATE INDEX IX_FE_BUZON_CORREOS_MSGID ON dbo.FE_BUZON_CORREOS (MESSAGE_ID);
GO
IF OBJECT_ID('dbo.FE_BUZON_DOCUMENTOS', 'U') IS NULL
CREATE TABLE dbo.FE_BUZON_DOCUMENTOS (
    ID                 int IDENTITY(1,1) NOT NULL CONSTRAINT PK_FE_BUZON_DOCUMENTOS PRIMARY KEY,
    CLAVE              varchar(50)    NOT NULL CONSTRAINT UQ_FE_BUZON_DOCUMENTOS_CLAVE UNIQUE,
    TIPO_DOC           varchar(5)     NULL,    -- FE TE NC ND FEC FEE REP
    VERSION            varchar(6)     NULL,
    CONSECUTIVO        varchar(40)    NULL,
    FECHA_EMISION      datetime2(0)   NULL,    -- hora de Costa Rica
    FECHA_EMISION_TXT  varchar(40)    NULL,    -- valor original del XML
    NIT_EMISOR         varchar(40)    NULL,
    TIPO_ID_EMISOR     varchar(2)     NULL,
    NOMBRE             nvarchar(250)  NULL,    -- emisor (como DOC_ELEC_RECIBIDO.NOMBRE)
    EMAIL_EMISOR       varchar(160)   NULL,
    NIT_RECEPTOR       varchar(40)    NULL,
    NOMBRE_RECEPTOR    nvarchar(250)  NULL,
    PARA_LOS_PATICAS   bit            NOT NULL CONSTRAINT DF_FE_BUZON_DOC_PARA DEFAULT 0,
    CODIGO_ACTIVIDAD   varchar(6)     NULL,
    MONEDA             varchar(3)     NULL,
    TIPO_CAMBIO        decimal(18,5)  NULL,
    TOTALSERVGRAVADOS  decimal(18,5)  NULL,
    TOTALSERVEXENTOS   decimal(18,5)  NULL,
    TOTAL_SERVICIOS_EXONERADOS  decimal(18,5) NULL,
    TOTALMERCANCIASGRAVADAS     decimal(18,5) NULL,
    TOTALMERCANCIASEXENTAS      decimal(18,5) NULL,
    TOTAL_MERCANCIAS_EXONERADAS decimal(18,5) NULL,
    TOTALGRAVADO       decimal(18,5)  NULL,
    TOTALEXENTO        decimal(18,5)  NULL,
    TOTAL_EXONERADO    decimal(18,5)  NULL,
    TOTALVENTA         decimal(18,5)  NULL,
    TOTALDESCUENTOS    decimal(18,5)  NULL,
    TOTALVENTANETA     decimal(18,5)  NULL,
    TOTALIMPUESTO      decimal(18,5)  NULL,
    TOTAL_IVA_DEVUELTO decimal(18,5)  NULL,
    TOTAL_OTROS_CARGOS decimal(18,5)  NULL,
    TOTALCOMPROBANTE   decimal(18,5)  NULL,
    ESTADO_HACIENDA    varchar(20)    NULL,    -- aceptado | aceptado_parcial | rechazado (MensajeHacienda); NULL = sin respuesta
    MENSAJE_HACIENDA   nvarchar(max)  NULL,
    TIENE_XML          bit NOT NULL CONSTRAINT DF_FE_BUZON_DOC_X DEFAULT 0,
    TIENE_RESPUESTA    bit NOT NULL CONSTRAINT DF_FE_BUZON_DOC_R DEFAULT 0,
    TIENE_PDF          bit NOT NULL CONSTRAINT DF_FE_BUZON_DOC_P DEFAULT 0,
    CARPETA_ARCHIVOS   varchar(200)   NULL,    -- relativa a /var/lib/lospaticas/fe/buzon
    CORREO_ID          int            NULL,    -- primer correo en que llego
    FECHA_INGRESO      datetime2(0)   NOT NULL CONSTRAINT DF_FE_BUZON_DOC_FI DEFAULT SYSDATETIME(),
    FECHA_ACTUALIZADO  datetime2(0)   NULL
);
GO
IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE object_id = OBJECT_ID('dbo.FE_BUZON_DOCUMENTOS') AND name = 'IX_FE_BUZON_DOC_FECHA')
    CREATE INDEX IX_FE_BUZON_DOC_FECHA ON dbo.FE_BUZON_DOCUMENTOS (FECHA_EMISION DESC);
GO
IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE object_id = OBJECT_ID('dbo.FE_BUZON_DOCUMENTOS') AND name = 'IX_FE_BUZON_DOC_EMISOR')
    CREATE INDEX IX_FE_BUZON_DOC_EMISOR ON dbo.FE_BUZON_DOCUMENTOS (NIT_EMISOR);
GO
IF OBJECT_ID('dbo.FE_BUZON_ARCHIVOS', 'U') IS NULL
CREATE TABLE dbo.FE_BUZON_ARCHIVOS (
    ID         int IDENTITY(1,1) NOT NULL CONSTRAINT PK_FE_BUZON_ARCHIVOS PRIMARY KEY,
    CORREO_ID  int           NOT NULL,
    CLAVE      varchar(50)   NULL,
    CLASE      varchar(12)   NOT NULL,         -- xml | respuesta | receptor | pdf
    NOMBRE     nvarchar(260) NULL,             -- nombre original del adjunto
    ORIGEN_ZIP nvarchar(260) NULL,
    RUTA       varchar(400)  NOT NULL,         -- relativa a /var/lib/lospaticas/fe/buzon
    SHA256     char(64)      NOT NULL,
    TAMANO     int           NULL,
    FECHA      datetime2(0)  NOT NULL CONSTRAINT DF_FE_BUZON_ARCH_F DEFAULT SYSDATETIME()
);
GO
IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE object_id = OBJECT_ID('dbo.FE_BUZON_ARCHIVOS') AND name = 'IX_FE_BUZON_ARCH_CLAVE')
    CREATE INDEX IX_FE_BUZON_ARCH_CLAVE ON dbo.FE_BUZON_ARCHIVOS (CLAVE, CLASE);
GO
IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE object_id = OBJECT_ID('dbo.FE_BUZON_ARCHIVOS') AND name = 'IX_FE_BUZON_ARCH_CORREO')
    CREATE INDEX IX_FE_BUZON_ARCH_CORREO ON dbo.FE_BUZON_ARCHIVOS (CORREO_ID);
GO
