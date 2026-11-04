<?php
declare(strict_types=1);
require __DIR__ . '/lib.php';
$csrf = htmlspecialchars($_SESSION['csrf']);
?><!doctype html>
<html lang="es">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Panel Hacienda · Los Paticas</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
 body{background:#f4f6f9}
 .log{font-family:ui-monospace,Consolas,monospace;font-size:.8rem;white-space:pre-wrap;word-break:break-word}
 .log tr td:first-child{white-space:nowrap}
 .nivel-Error{color:#b02a37;font-weight:600}.nivel-Warning{color:#997404}.nivel-Info{color:#0a58ca}.nivel-Debug{color:#6c757d}
 pre.xml{max-height:55vh;overflow:auto;background:#1e1e1e;color:#d4d4d4;padding:.75rem;font-size:.75rem;border-radius:.375rem}
 .table-sm td,.table-sm th{vertical-align:middle}
 #tblLogs tbody tr.nuevo{animation:flash 2s}
 @keyframes flash{from{background:#fff3cd}to{background:transparent}}
</style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark mb-3">
 <div class="container-fluid">
  <span class="navbar-brand"><i class="bi bi-receipt"></i> Panel de facturación electrónica (Hacienda)</span>
  <span class="text-light small" id="hdr"></span>
 </div>
</nav>
<div class="container-fluid">
 <div id="avisoCopia" class="alert alert-warning d-none"></div>

 <div class="row g-3 mb-3" id="tarjetas"></div>

 <ul class="nav nav-tabs" role="tablist">
  <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabLogs"><i class="bi bi-terminal"></i> Registro en vivo</button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabDocs" id="btnTabDocs"><i class="bi bi-file-earmark-text"></i> Comprobantes</button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabCola" id="btnTabCola"><i class="bi bi-hourglass-split"></i> Cola de envío</button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabSrv"><i class="bi bi-gear"></i> Servicio</button></li>
 </ul>
 <div class="tab-content bg-white border border-top-0 p-3 mb-4">

  <!-- REGISTRO -->
  <div class="tab-pane fade show active" id="tabLogs">
   <form class="row g-2 align-items-end mb-2" id="fLogs">
    <div class="col-auto"><label class="form-label small">Fuente</label>
     <select class="form-select form-select-sm" name="src">
      <option value="bitacora">Bitácora (BD dbo.BITACORA)</option>
      <option value="sender">Salida del servicio (api44/logs/sender.log)</option>
      <option value="errores">Errores api (fe/API/api/errors/_errors.log)</option>
      <option value="php">Log PHP (solo líneas de api44/Hacienda)</option>
     </select></div>
    <div class="col-auto"><label class="form-label small">Nivel</label>
     <select class="form-select form-select-sm" name="nivel"><option value="">Todos</option><option>Error</option><option>Warning</option><option>Info</option><option>Debug</option></select></div>
    <div class="col-auto"><label class="form-label small">Texto</label><input class="form-control form-control-sm" name="q" placeholder="buscar..."></div>
    <div class="col-auto"><label class="form-label small">Documento</label><input class="form-control form-control-sm" name="doc" placeholder="F-0000..." size="16"></div>
    <div class="col-auto"><label class="form-label small">Desde</label><input type="datetime-local" class="form-control form-control-sm" name="desde"></div>
    <div class="col-auto"><label class="form-label small">Hasta</label><input type="datetime-local" class="form-control form-control-sm" name="hasta"></div>
    <div class="col-auto"><label class="form-label small">Filas</label>
     <select class="form-select form-select-sm" name="limit"><option>50</option><option selected>100</option><option>200</option><option>500</option></select></div>
    <div class="col-auto"><button class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Buscar</button></div>
    <div class="col-auto form-check form-switch ms-2">
     <input class="form-check-input" type="checkbox" id="vivo" checked><label class="form-check-label small" for="vivo">En vivo (cada 3 s)</label></div>
    <div class="col-auto small text-muted" id="logInfo"></div>
   </form>
   <div class="table-responsive" style="max-height:65vh;overflow:auto">
    <table class="table table-sm table-hover log mb-1" id="tblLogs"><thead class="table-light sticky-top"><tr><th>Fecha/hora</th><th>Nivel</th><th>Documento</th><th>Mensaje</th></tr></thead><tbody></tbody></table>
   </div>
   <button class="btn btn-outline-secondary btn-sm" id="btnMas"><i class="bi bi-chevron-double-down"></i> Más antiguos</button>
  </div>

  <!-- COMPROBANTES -->
  <div class="tab-pane fade" id="tabDocs">
   <form class="row g-2 align-items-end mb-2" id="fDocs">
    <div class="col-auto"><label class="form-label small">Estado</label>
     <select class="form-select form-select-sm" name="estado"><option value="">Todos</option><option value="aceptado">Aceptado</option><option value="rechazado">Rechazado</option><option value="error">Error</option><option value="pendiente">Pendiente de respuesta</option><option value="reintentar">Marcado para reintento</option><option value="procesando">Procesando</option></select></div>
    <div class="col-auto"><label class="form-label small">Tipo</label>
     <select class="form-select form-select-sm" name="tipo"><option value="">Todos</option><option>FE</option><option>TE</option><option>NC</option><option>ND</option></select></div>
    <div class="col-auto"><label class="form-label small">Buscar</label><input class="form-control form-control-sm" name="q" placeholder="número, clave, cédula, correo"></div>
    <div class="col-auto"><label class="form-label small">Desde</label><input type="date" class="form-control form-control-sm" name="desde"></div>
    <div class="col-auto"><label class="form-label small">Hasta</label><input type="date" class="form-control form-control-sm" name="hasta"></div>
    <div class="col-auto"><button class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Buscar</button></div>
    <div class="col-auto small text-muted" id="docsInfo"></div>
   </form>
   <div class="table-responsive">
    <table class="table table-sm table-hover" id="tblDocs"><thead class="table-light"><tr><th>Fecha</th><th>Tipo</th><th>Número</th><th>Consecutivo</th><th>Clave</th><th>Receptor</th><th class="text-end">Total</th><th>Estado</th><th></th></tr></thead><tbody></tbody></table>
   </div>
   <nav><ul class="pagination pagination-sm" id="pagDocs"></ul></nav>
  </div>

  <!-- COLA -->
  <div class="tab-pane fade" id="tabCola">
   <p class="small text-muted">Documentos que el próximo lote del servicio tomaría (misma consulta que <code>ProcesarPendientesEnvio</code>, solo lectura; el servicio procesa <em>DOCS_LOTE</em> por corrida).</p>
   <table class="table table-sm" id="tblCola"><thead class="table-light"><tr><th>Fecha</th><th>Tipo</th><th>Documento</th><th>Cédula receptor</th><th>Estado previo</th></tr></thead><tbody></tbody></table>
  </div>

  <!-- SERVICIO -->
  <div class="tab-pane fade" id="tabSrv">
   <div class="row g-3">
    <div class="col-lg-6">
     <h6>Servicio de envío <code>paticas-hacienda-sender</code></h6>
     <table class="table table-sm" id="tblSrv"><tbody></tbody></table>
     <button class="btn btn-success btn-sm" id="btnStart"><i class="bi bi-play-fill"></i> Iniciar envío automático</button>
     <button class="btn btn-danger btn-sm" id="btnStop"><i class="bi bi-stop-fill"></i> Detener envío automático</button>
     <div class="small text-muted mt-2">Iniciar/Detener controla el <em>timer</em> (una corrida de <code>admin.php</code> por minuto). Detener no interrumpe un lote en curso: termina el actual y no inicia otro. El arranque está bloqueado hasta que se realice el corte (archivo <code><?= htmlspecialchars(PH_GUARD) ?></code>), para que nunca haya dos servidores enviando.</div>
     <div id="srvMsg" class="mt-2"></div>
    </div>
    <div class="col-lg-6">
     <h6>Base de datos</h6>
     <table class="table table-sm" id="tblBd"><tbody></tbody></table>
    </div>
   </div>
  </div>
 </div>
</div>

<!-- DETALLE -->
<div class="modal fade" id="mDoc" tabindex="-1"><div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content">
 <div class="modal-header"><h5 class="modal-title" id="mDocTitulo"></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
 <div class="modal-body">
  <div id="mDocResumen" class="mb-2"></div>
  <ul class="nav nav-pills mb-2"><li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pXml">XML enviado</button></li>
   <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pResp">Respuesta de Hacienda</button></li>
   <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pBit">Bitácora del documento</button></li></ul>
  <div class="tab-content">
   <div class="tab-pane fade show active" id="pXml"><pre class="xml" id="xmlEnv"></pre></div>
   <div class="tab-pane fade" id="pResp"><div id="respTxt" class="small mb-2"></div><pre class="xml" id="xmlResp"></pre></div>
   <div class="tab-pane fade" id="pBit"><table class="table table-sm log"><tbody id="bitDoc"></tbody></table></div>
  </div>
 </div>
 <div class="modal-footer">
  <span class="small text-muted me-auto" id="mDocMsg"></span>
  <button class="btn btn-outline-warning btn-sm" id="btnSim"><i class="bi bi-eye"></i> Simular reintento</button>
  <button class="btn btn-warning btn-sm" id="btnRe"><i class="bi bi-arrow-repeat"></i> Reintentar</button>
 </div>
</div></div></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const CSRF = <?= json_encode($_SESSION['csrf']) ?>;
const $ = s => document.querySelector(s);
const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const fmt = n => n == null ? '' : Number(n).toLocaleString('es-CR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
const BADGE = {aceptado:'success', rechazado:'danger', error:'danger', pendiente:'secondary', reintentar:'warning', procesando:'info'};
const badge = e => `<span class="badge text-bg-${BADGE[e] || 'secondary'}">${esc(e)}</span>`;
async function api(a, params = {}, post = null) {
  const url = 'api.php?a=' + a + '&' + new URLSearchParams(params);
  const opt = post ? {method: 'POST', body: new URLSearchParams({...post, csrf: CSRF})} : {};
  const r = await fetch(url, opt); const j = await r.json().catch(() => ({ok: false, error: 'Respuesta inválida (' + r.status + ')'}));
  return j;
}
function formData(f) { const o = {}; new FormData(f).forEach((v, k) => { if (v !== '') o[k] = String(v).replace('T', ' '); }); return o; }

/* ---------- estado ---------- */
let corte = false;
async function cargarEstado() {
  const s = await api('status'); if (!s.ok) { $('#hdr').textContent = s.error; return; }
  corte = s.corte_realizado;
  $('#hdr').textContent = `Usuario: ${s.usuario} · ${s.ahora} (hora de Costa Rica)`;
  const t = s.timer || {}, sv = s.servicio || {};
  const activo = t.ActiveState === 'active';
  const av = $('#avisoCopia');
  if (!corte) { av.classList.remove('d-none'); av.innerHTML = `<i class="bi bi-exclamation-triangle"></i> <b>Antes del corte:</b> el envío a Hacienda sigue en el servidor Windows 172.16.12.3. La BD <code>${esc(s.bd.database)}</code> en <code>${esc(s.bd.host)}</code> es una <b>copia</b>${s.bd.restaurada ? ' restaurada el ' + esc(s.bd.restaurada) : ''}; el servicio de este servidor está bloqueado y "Reintentar" solo simula.`; }
  else av.classList.add('d-none');
  const c = s.conteos;
  const card = (t, v, cls, ic) => `<div class="col-6 col-md-4 col-xl-2"><div class="card border-${cls}"><div class="card-body py-2"><div class="small text-muted"><i class="bi bi-${ic}"></i> ${t}</div><div class="fs-4 fw-semibold text-${cls}">${v}</div></div></div></div>`;
  $('#tarjetas').innerHTML =
    card('Servicio', activo ? 'Iniciado' : 'Detenido', activo ? 'success' : 'secondary', activo ? 'play-circle' : 'stop-circle') +
    card('En cola de envío', c.cola, 'primary', 'hourglass-split') +
    card('Pendientes de respuesta', c.pendiente_respuesta, 'secondary', 'clock') +
    card('Con error', c.error + (c.reintentar ? ` <small class="fs-6">(+${c.reintentar} reintento)</small>` : ''), c.error ? 'danger' : 'success', 'x-octagon') +
    card('Aceptados hoy', c.aceptado_hoy, 'success', 'check-circle') +
    card('Rechazados hoy', c.rechazado_hoy, c.rechazado_hoy ? 'danger' : 'secondary', 'slash-circle');
  const row = (k, v) => `<tr><th class="w-50">${k}</th><td>${v}</td></tr>`;
  $('#tblSrv tbody').innerHTML = row('Corte realizado', corte ? '<span class="badge text-bg-success">Sí</span>' : '<span class="badge text-bg-warning">No (bloqueado)</span>') +
    row('Timer', `${esc(t.ActiveState)} · ${esc(t.UnitFileState)}`) + row('Última activación', esc(t.LastTriggerUSec || '-')) + row('Próxima', esc(t.NextElapseUSecRealtime || '-')) +
    row('Última corrida', `${esc(sv.ActiveState)}/${esc(sv.SubState)} · resultado ${esc(sv.Result)} · código ${esc(sv.ExecMainStatus)}`) +
    row('Inicio / fin', `${esc(sv.ExecMainStartTimestamp || '-')}<br>${esc(sv.ExecMainExitTimestamp || '-')}`);
  $('#tblBd tbody').innerHTML = row('Servidor / BD', `${esc(s.bd.host)} / ${esc(s.bd.database)}`) + row('Restaurada', esc(s.bd.restaurada || '-')) +
    row('Última bitácora', esc(s.ultima_bitacora)) + row('Último comprobante', esc(s.ultimo_documento));
  $('#btnStart').disabled = !corte || activo; $('#btnStop').disabled = !activo;
}

/* ---------- registro ---------- */
let curNuevo = null, curAntiguo = null, timer = null;
function filaLog(r, nuevo) {
  return `<tr class="${nuevo ? 'nuevo' : ''}"><td>${esc(r.f)}</td><td class="nivel-${esc(r.nivel)}">${esc(r.nivel)}</td><td>${esc(r.doc)}</td><td>${esc(r.texto)}</td></tr>`;
}
async function cargarLogs(modo) {
  const p = formData($('#fLogs'));
  if (modo === 'vivo') { if (curNuevo == null) return; p.since = curNuevo; delete p.desde; delete p.hasta; }
  if (modo === 'mas') p.before = curAntiguo;
  const j = await api('logs', p); const tb = $('#tblLogs tbody');
  if (!j.ok) { $('#logInfo').textContent = j.error; return; }
  const html = j.rows.map(r => filaLog(r, modo === 'vivo')).join('');
  if (modo === 'vivo') { if (j.rows.length) tb.insertAdjacentHTML('afterbegin', html); }
  else if (modo === 'mas') tb.insertAdjacentHTML('beforeend', html);
  else tb.innerHTML = html || '<tr><td colspan="4" class="text-muted">Sin registros para el filtro.</td></tr>';
  if (modo !== 'mas' && j.cursor_nuevo != null) curNuevo = j.cursor_nuevo;
  if (modo !== 'vivo') { curAntiguo = j.cursor_antiguo; $('#btnMas').disabled = !j.mas; }
  while (tb.rows.length > 2000) tb.deleteRow(-1);
  $('#logInfo').textContent = (j.aviso || '') + ` ${tb.rows.length} filas · actualizado ${new Date().toLocaleTimeString('es-CR')}`;
}
function programarVivo() { clearInterval(timer); if ($('#vivo').checked) timer = setInterval(() => { if (!document.hidden) cargarLogs('vivo'); }, 3000); }
$('#fLogs').addEventListener('submit', e => { e.preventDefault(); curNuevo = null; cargarLogs('nuevo'); });
$('#fLogs [name=src]').addEventListener('change', () => { curNuevo = null; cargarLogs('nuevo'); });
$('#vivo').addEventListener('change', programarVivo);
$('#btnMas').addEventListener('click', () => cargarLogs('mas'));

/* ---------- comprobantes ---------- */
let pagina = 1;
async function cargarDocs(p = 1) {
  pagina = p; const j = await api('docs', {...formData($('#fDocs')), page: p, size: 50});
  if (!j.ok) { $('#docsInfo').textContent = j.error; return; }
  $('#tblDocs tbody').innerHTML = j.rows.map(r => `<tr>
    <td class="text-nowrap">${esc(r.fecha)}</td><td>${esc(r.tipo)}</td><td class="text-nowrap">${esc(r.numero)}</td><td class="small">${esc(r.consecutivo)}</td>
    <td class="small text-break" style="max-width:16rem">${esc(r.clave)}</td><td class="small">${esc(r.receptor)}<br><span class="text-muted">${esc(r.email)}</span></td>
    <td class="text-end text-nowrap">${fmt(r.total)}</td><td>${badge(r.estado)}${r.error && r.estado !== 'aceptado' ? `<div class="small text-muted text-truncate" style="max-width:18rem" title="${esc(r.error)}">${esc(r.error)}</div>` : ''}</td>
    <td class="text-nowrap"><button class="btn btn-outline-primary btn-sm" onclick="verDoc('${esc(r.tipo)}','${esc(r.numero)}')"><i class="bi bi-eye"></i> Ver</button></td></tr>`).join('') ||
    '<tr><td colspan="9" class="text-muted">Sin comprobantes para el filtro.</td></tr>';
  $('#docsInfo').textContent = `${j.total.toLocaleString('es-CR')} comprobantes`;
  const n = Math.ceil(j.total / j.size), pg = $('#pagDocs'); let h = '';
  const it = (i, t, dis, act) => `<li class="page-item ${dis ? 'disabled' : ''} ${act ? 'active' : ''}"><a class="page-link" href="#" data-p="${i}">${t}</a></li>`;
  h += it(1, '«', p === 1) + it(p - 1, '‹', p === 1);
  for (let i = Math.max(1, p - 3); i <= Math.min(n, p + 3); i++) h += it(i, i, false, i === p);
  h += it(p + 1, '›', p >= n) + it(n, '»', p >= n);
  pg.innerHTML = n > 1 ? h : '';
}
$('#pagDocs').addEventListener('click', e => { const a = e.target.closest('a'); if (a) { e.preventDefault(); cargarDocs(+a.dataset.p); } });
$('#fDocs').addEventListener('submit', e => { e.preventDefault(); cargarDocs(1); });
$('#btnTabDocs').addEventListener('shown.bs.tab', () => { if (!$('#tblDocs tbody').rows.length) cargarDocs(1); });

let docActual = null; const modal = new bootstrap.Modal('#mDoc');
async function verDoc(tipo, numero) {
  const j = await api('doc', {tipo, numero}); if (!j.ok) { alert(j.error); return; }
  const d = j.doc; docActual = {tipo, numero};
  $('#mDocTitulo').innerHTML = `${esc(tipo)} ${esc(numero)} ${badge(d.ESTADO_UI)}`;
  $('#mDocResumen').innerHTML = `<div class="row small">
    <div class="col-md-6"><b>Clave:</b> ${esc(d.SELLO_SAT)}<br><b>Consecutivo:</b> ${esc(j.consecutivo)}<br><b>Fecha emisión:</b> ${esc(d.FECHATIMBRADO)} · <b>registro:</b> ${esc(d.FECHA_HORA)}</div>
    <div class="col-md-6"><b>Receptor:</b> ${esc(j.receptor_nombre)} (${esc(d.NIT_RECEPTOR)}) ${esc(d.EMAIL_RECEPTOR)}<br><b>Total:</b> ${fmt(d.TOTALCOMPROBANTE)} · <b>Impuesto:</b> ${fmt(d.TOTALIMPUESTO)}<br>
    <b>Enviado:</b> ${esc(d.ENVIADO)} · <b>Aceptado:</b> ${esc(d.ACEPTADO)} · <b>Correo:</b> ${esc(d.EMAIL_ENVIADO)}</div>
    <div class="col-12 mt-1"><b>Último mensaje:</b> ${esc(d.ULTIMO_ERROR)}</div></div>`;
  $('#xmlEnv').textContent = j.xml || '(sin XML guardado)';
  $('#xmlResp').textContent = j.xml_respuesta || '(sin respuesta de Hacienda)';
  $('#respTxt').innerHTML = j.mensaje_hacienda ? `<b>DetalleMensaje:</b> ${esc(j.mensaje_hacienda)}` : esc(d.RESPUESTA_HACIENDA || '');
  $('#bitDoc').innerHTML = j.bitacora.map(r => filaLog(r)).join('') || '<tr><td class="text-muted">Sin entradas.</td></tr>';
  $('#btnRe').disabled = !j.puede_reintentar || !corte; $('#btnSim').disabled = !j.puede_reintentar;
  $('#mDocMsg').textContent = j.puede_reintentar ? (corte ? '' : 'Antes del corte solo se permite simular.') : 'Reintentar solo aplica a documentos en estado "error" no enviados (los rechazados requieren un documento nuevo).';
  modal.show();
}
async function reintentar(dry) {
  if (!docActual) return;
  if (!dry && !confirm(`¿Marcar ${docActual.tipo} ${docActual.numero} para reenvío a Hacienda?`)) return;
  const j = await api('reintentar', {}, {...docActual, dry_run: dry ? '1' : '0'});
  $('#mDocMsg').innerHTML = (j.ok ? '<span class="text-success">' : '<span class="text-danger">') + esc(j.mensaje || j.error) + '</span>';
  if (j.ok && !j.simulacion) { cargarDocs(pagina); cargarEstado(); }
}
$('#btnSim').addEventListener('click', () => reintentar(true));
$('#btnRe').addEventListener('click', () => reintentar(false));

/* ---------- cola ---------- */
$('#btnTabCola').addEventListener('shown.bs.tab', async () => {
  const j = await api('cola');
  $('#tblCola tbody').innerHTML = (j.rows || []).map(r => `<tr><td>${esc(r.FECHA)}</td><td>${esc(r.TIPO_DOCUMENTO)}</td><td>${esc(r.DOCUMENTO)}</td><td>${esc(r.NIT)}</td><td>${esc(r.ESTADO)}</td></tr>`).join('') || `<tr><td colspan="5" class="text-muted">${esc(j.error || 'Cola vacía.')}</td></tr>`;
});

/* ---------- servicio ---------- */
async function servicio(accion) {
  if (accion === 'start' && !confirm('¿Iniciar el envío automático a Hacienda desde este servidor? Confirme que el de Windows está detenido.')) return;
  const j = await api('servicio', {}, {accion});
  $('#srvMsg').innerHTML = `<div class="alert alert-${j.ok ? 'success' : 'danger'} py-1 small">${esc(j.ok ? 'Listo: timer ' + (j.timer?.ActiveState || '') : j.error || j.salida)}</div>`;
  cargarEstado();
}
$('#btnStart').addEventListener('click', () => servicio('start'));
$('#btnStop').addEventListener('click', () => servicio('stop'));

cargarEstado(); setInterval(() => { if (!document.hidden) cargarEstado(); }, 15000);
cargarLogs('nuevo'); programarVivo();
</script>
</body>
</html>
