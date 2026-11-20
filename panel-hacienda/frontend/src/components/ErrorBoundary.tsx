import { Component, Fragment, type ReactNode } from 'react';
import { AlertTriangle, RotateCcw } from 'lucide-react';

// Evita la pantalla en blanco: si una vista falla al dibujarse se muestra el error y un boton para reintentar.
// No usar div.contents: display:contents + hijos en Fragment deja nodos huérfanos al cambiar de pestaña.
export class ErrorBoundary extends Component<{ children: ReactNode; nombre: string }, { error: Error | null; n: number }> {
  state = { error: null as Error | null, n: 0 };
  static getDerivedStateFromError(error: Error) { return { error }; }
  componentDidCatch(error: Error) { console.error(`Error en la vista ${this.props.nombre}:`, error); }
  render() {
    if (!this.state.error) return <Fragment key={this.state.n}>{this.props.children}</Fragment>;
    return <div className="mx-auto mt-10 max-w-lg rounded-xl border border-danger/30 bg-card p-6 text-center shadow-soft">
      <AlertTriangle className="mx-auto h-10 w-10 text-danger" />
      <h2 className="mt-3 text-lg font-semibold">No se pudo mostrar “{this.props.nombre}”</h2>
      <p className="mt-1 text-sm text-muted-foreground">Ocurrió un error inesperado en esta pantalla. El resto del panel sigue funcionando.</p>
      <pre className="mt-4 max-h-32 overflow-auto rounded-lg bg-muted p-3 text-left text-xs text-muted-foreground">{String(this.state.error.message || this.state.error)}</pre>
      <button onClick={() => this.setState(s => ({ error: null, n: s.n + 1 }))}
        className="mt-4 inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary/90"><RotateCcw className="h-4 w-4" />Reintentar</button>
    </div>;
  }
}
