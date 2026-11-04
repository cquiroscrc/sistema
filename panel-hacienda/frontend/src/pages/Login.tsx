import { useState } from 'react';
import { Receipt, Lock, User, Moon, Sun, ShieldCheck } from 'lucide-react';
import { api } from '../lib/api';
import { Button, Input, Label } from '../components/ui';

export default function Login({ onOk, oscuro, onTema }: { onOk: (u: { usuario: string; nombre: string }) => void; oscuro: boolean; onTema: () => void }) {
  const [u, setU] = useState(''); const [p, setP] = useState(''); const [err, setErr] = useState(''); const [cargando, setCargando] = useState(false);
  const enviar = async (e: React.FormEvent) => {
    e.preventDefault(); setErr(''); setCargando(true);
    try {
      const j = await api('login', {}, { usuario: u, password: p });
      if (j.ok) onOk({ usuario: j.usuario, nombre: j.nombre }); else { setErr(j.error || 'No se pudo ingresar'); setP(''); }
    } finally { setCargando(false); }
  };
  return <div className="relative flex min-h-screen items-center justify-center overflow-hidden bg-gradient-to-br from-teal-900 via-slate-900 to-slate-950 p-4">
    <div className="pointer-events-none absolute -left-40 -top-40 h-96 w-96 rounded-full bg-teal-500/20 blur-3xl" />
    <div className="pointer-events-none absolute -bottom-40 -right-40 h-96 w-96 rounded-full bg-cyan-400/10 blur-3xl" />
    <button onClick={onTema} className="absolute right-4 top-4 rounded-md p-2 text-white/70 hover:bg-white/10" aria-label="Tema">{oscuro ? <Sun className="h-4 w-4" /> : <Moon className="h-4 w-4" />}</button>
    <form onSubmit={enviar} className="relative w-full max-w-sm rounded-2xl border border-white/10 bg-card p-8 shadow-2xl">
      <div className="mb-6 flex flex-col items-center text-center">
        <div className="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-primary text-primary-foreground shadow-lg"><Receipt className="h-6 w-6" /></div>
        <h1 className="text-lg font-semibold">Panel de facturación electrónica</h1>
        <p className="text-sm text-muted-foreground">Ingrese con su usuario del sistema PATICAS</p>
      </div>
      <div className="space-y-4">
        <div><Label htmlFor="u">Usuario</Label>
          <div className="relative"><User className="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground" /><Input id="u" className="pl-9" autoComplete="username" autoFocus value={u} onChange={e => setU(e.target.value)} required /></div></div>
        <div><Label htmlFor="p">Contraseña</Label>
          <div className="relative"><Lock className="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground" /><Input id="p" type="password" className="pl-9" autoComplete="current-password" value={p} onChange={e => setP(e.target.value)} required /></div></div>
        {err && <div className="rounded-md bg-danger/10 px-3 py-2 text-sm text-danger">{err}</div>}
        <Button className="w-full" loading={cargando} type="submit">Ingresar</Button>
      </div>
      <p className="mt-6 flex items-center justify-center gap-1.5 text-xs text-muted-foreground"><ShieldCheck className="h-3.5 w-3.5" />Acceso registrado en la bitácora</p>
    </form>
  </div>;
}
