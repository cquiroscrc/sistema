<?php
/**
 * Migraciones de BD del panel-hacienda (objetos propios en inventario: PANEL_MIGRACIONES, FE_BUZON_*).
 * Archivos: db/migrations/NNNN_nombre.sql, idempotentes, lotes separados por "GO".
 * Linea opcional "-- objetos: dbo.TABLA, dbo.TABLA.INDICE" -> se verifica que existan despues de aplicar y en --estado.
 * Cada archivo se aplica en su propia transaccion y se registra en dbo.PANEL_MIGRACIONES (nombre + SHA-256).
 * No incluye la correccion de rutas (eso es la accion "Rutas" del panel).
 */
declare(strict_types=1);

const MIG_DIR   = __DIR__ . '/migrations';
const MIG_LOG   = '/var/lib/lospaticas/panel/migraciones.log';
const MIG_CACHE = '/var/lib/lospaticas/panel/migraciones-cache.json';
const MIG_CACHE_SEG = 120;

function mig_log(string $m): void
{
    $l = date('Y-m-d H:i:s') . ' ' . $m . "\n";
    @file_put_contents(MIG_LOG, $l, FILE_APPEND | LOCK_EX);
    if (PHP_SAPI === 'cli') fwrite(STDERR, $l);
}

/** Lista ordenada de migraciones: [nombre, ruta, sha, sql, objetos[]] */
function mig_archivos(): array
{
    $r = [];
    foreach (glob(MIG_DIR . '/*.sql') ?: [] as $f) {
        $n = basename($f);
        if (!preg_match('/^\d{4}_[a-z0-9_]+\.sql$/', $n)) continue;
        $sql = (string)file_get_contents($f);
        $obj = preg_match('/^--\s*objetos:\s*(.+)$/mi', $sql, $m) ? array_values(array_filter(array_map('trim', explode(',', $m[1])))) : [];
        $r[$n] = ['nombre' => $n, 'sha' => hash('sha256', str_replace("\r", '', $sql)), 'sql' => $sql, 'objetos' => $obj];
    }
    ksort($r, SORT_STRING);
    return array_values($r);
}

function mig_lotes(string $sql): array
{
    $r = [];
    foreach (preg_split('/^\s*GO\s*$/mi', str_replace("\r", '', $sql)) as $b)
        if (trim(preg_replace('/^\s*--.*$/m', '', $b)) !== '') $r[] = trim($b);
    return $r;
}

/** Solo pruebas: reescribe "dbo." a otro esquema (migrar.php --esquema=...). */
$GLOBALS['MIG_ESQ'] = '';
function mig_q(string $s): string { return $GLOBALS['MIG_ESQ'] !== '' ? preg_replace('/\bdbo\./', $GLOBALS['MIG_ESQ'] . '.', $s) : $s; }
final class MigPDO extends PDO
{
    public function exec(string $statement): int|false { return parent::exec(mig_q($statement)); }
    public function prepare(string $query, array $options = []): PDOStatement|false { return parent::prepare(mig_q($query), $options); }
    public function query(string $query, ?int $fetchMode = null, mixed ...$fetchModeArgs): PDOStatement|false
    { return $fetchMode === null ? parent::query(mig_q($query)) : parent::query(mig_q($query), $fetchMode, ...$fetchModeArgs); }
}

function mig_tabla(PDO $db): void
{
    $db->exec("IF OBJECT_ID('dbo.PANEL_MIGRACIONES', 'U') IS NULL
CREATE TABLE dbo.PANEL_MIGRACIONES (
    NOMBRE varchar(200) NOT NULL CONSTRAINT PK_PANEL_MIGRACIONES PRIMARY KEY, SHA256 char(64) NOT NULL,
    APLICADA datetime2(0) NOT NULL CONSTRAINT DF_PANEL_MIGRACIONES_F DEFAULT SYSDATETIME(), DURACION_MS int NULL,
    USUARIO nvarchar(120) NULL, HOST nvarchar(120) NULL)");
}

/** Objetos faltantes de una lista "dbo.TABLA" / "dbo.TABLA.INDICE". */
function mig_faltantes(PDO $db, array $objetos): array
{
    $f = [];
    foreach ($objetos as $o) {
        $p = explode('.', $o);
        if (count($p) === 3) { $st = $db->prepare('SELECT COUNT(*) FROM sys.indexes WHERE object_id = OBJECT_ID(?) AND name = ?'); $st->execute([mig_q($p[0] . '.' . $p[1]), $p[2]]); }
        else { $st = $db->prepare('SELECT CASE WHEN OBJECT_ID(?) IS NULL THEN 0 ELSE 1 END'); $st->execute([mig_q($o)]); }
        if (!(int)$st->fetchColumn()) $f[] = $o;
        $st->closeCursor();
    }
    return $f;
}

/** Estado: lista de migraciones con aplicada/fecha/sha_distinto/faltantes. No crea nada. */
function mig_estado(PDO $db, bool $verificar = true): array
{
    $hechas = [];
    if ((int)$db->query("SELECT CASE WHEN OBJECT_ID('dbo.PANEL_MIGRACIONES', 'U') IS NULL THEN 0 ELSE 1 END")->fetchColumn())
        foreach ($db->query('SELECT NOMBRE, SHA256, CONVERT(varchar(19), APLICADA, 120) APLICADA, USUARIO FROM dbo.PANEL_MIGRACIONES')->fetchAll(PDO::FETCH_ASSOC) as $r) $hechas[$r['NOMBRE']] = $r;
    $out = [];
    foreach (mig_archivos() as $m) {
        $h = $hechas[$m['nombre']] ?? null;
        $out[] = ['nombre' => $m['nombre'], 'aplicada' => (bool)$h, 'fecha' => $h['APLICADA'] ?? null, 'usuario' => $h['USUARIO'] ?? null,
                  'modificada' => $h && trim($h['SHA256']) !== $m['sha'], 'faltantes' => $h && $verificar ? mig_faltantes($db, $m['objetos']) : []];
    }
    return $out;
}

/** Aplica las pendientes (y las que tienen objetos faltantes). Devuelve [aplicadas[], error|null]. */
function mig_aplicar(PDO $db, string $usuario): array
{
    mig_tabla($db);
    $aplicadas = [];
    foreach (mig_estado($db) as $e) {
        if ($e['aplicada'] && !$e['faltantes']) continue;
        $m = current(array_filter(mig_archivos(), fn($x) => $x['nombre'] === $e['nombre']));
        $t0 = microtime(true);
        try {
            $db->beginTransaction();
            foreach (mig_lotes($m['sql']) as $lote) $db->exec($lote);
            $falt = mig_faltantes($db, $m['objetos']);
            if ($falt) throw new RuntimeException('despues de aplicar faltan: ' . implode(', ', $falt));
            $ms = (int)round((microtime(true) - $t0) * 1000);
            $db->prepare('DELETE FROM dbo.PANEL_MIGRACIONES WHERE NOMBRE = ?')->execute([$m['nombre']]);
            $db->prepare('INSERT INTO dbo.PANEL_MIGRACIONES (NOMBRE, SHA256, DURACION_MS, USUARIO, HOST) VALUES (?, ?, ?, ?, ?)')
               ->execute([$m['nombre'], $m['sha'], $ms, mb_substr($usuario, 0, 120), mb_substr((string)gethostname(), 0, 120)]);
            $db->commit();
            mig_log("APLICADA {$m['nombre']} ({$ms} ms) por $usuario" . ($e['aplicada'] ? ' (reaplicada: faltaban objetos)' : ''));
            $aplicadas[] = $m['nombre'];
        } catch (Throwable $x) {
            if ($db->inTransaction()) $db->rollBack();
            $msg = "ERROR en {$m['nombre']}: " . $x->getMessage();
            mig_log($msg . " (por $usuario; se detiene)");
            @unlink(MIG_CACHE);
            return [$aplicadas, $msg];
        }
    }
    @unlink(MIG_CACHE);
    return [$aplicadas, null];
}

/** Resumen barato para el panel (cache de MIG_CACHE_SEG segundos por BD y conjunto de archivos). Nunca lanza. */
function mig_resumen(PDO $db, string $claveBd, bool $forzar = false): array
{
    try {
        $arch = mig_archivos();
        $clave = hash('sha256', $claveBd . '|' . implode(',', array_map(fn($m) => $m['nombre'] . ':' . $m['sha'], $arch)));
        if (!$forzar && is_readable(MIG_CACHE)) {
            $c = json_decode((string)file_get_contents(MIG_CACHE), true);
            if (is_array($c) && ($c['clave'] ?? '') === $clave && time() - (int)($c['ts'] ?? 0) < MIG_CACHE_SEG) return $c['r'];
        }
        $e = mig_estado($db);
        $pend = array_values(array_map(fn($x) => $x['nombre'], array_filter($e, fn($x) => !$x['aplicada'] || $x['faltantes'])));
        $r = ['total' => count($e), 'pendientes' => count($pend), 'nombres' => $pend,
              'modificadas' => array_values(array_map(fn($x) => $x['nombre'], array_filter($e, fn($x) => $x['modificada']))), 'revisado' => date('Y-m-d H:i:s')];
        @file_put_contents(MIG_CACHE, json_encode(['clave' => $clave, 'ts' => time(), 'r' => $r]), LOCK_EX);
        return $r;
    } catch (Throwable $x) {
        return ['total' => null, 'pendientes' => null, 'nombres' => [], 'modificadas' => [], 'error' => $x->getMessage()];
    }
}
