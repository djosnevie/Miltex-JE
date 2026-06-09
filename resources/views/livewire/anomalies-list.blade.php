@section('title', 'Anomalies')

<div>
    {{-- Filters --}}
    <div class="card" style="margin-bottom:20px;">
        <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
            <div>
                <label style="font-size:11px; color:var(--muted); display:block; margin-bottom:4px;">Journal</label>
                <select wire:model.live="journalId" style="background:var(--surface-3); border:1px solid var(--border); color:var(--text); padding:8px 12px; border-radius:8px; font-size:13px;">
                    <option value="">Tous les journaux</option>
                    @foreach($journals as $j)
                        <option value="{{ $j->id }}">{{ $j->original_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size:11px; color:var(--muted); display:block; margin-bottom:4px;">Sévérité</label>
                <select wire:model.live="severity" style="background:var(--surface-3); border:1px solid var(--border); color:var(--text); padding:8px 12px; border-radius:8px; font-size:13px;">
                    <option value="">Toutes</option>
                    <option value="critical">🔴 Critique</option>
                    <option value="warning">🟡 Avertissement</option>
                    <option value="info">🔵 Info</option>
                </select>
            </div>
            <div>
                <label style="font-size:11px; color:var(--muted); display:block; margin-bottom:4px;">Type</label>
                <select wire:model.live="type" style="background:var(--surface-3); border:1px solid var(--border); color:var(--text); padding:8px 12px; border-radius:8px; font-size:13px;">
                    <option value="">Tous</option>
                    <option value="mcf_error">Erreur MCF</option>
                    <option value="gap_sequence">Rupture de séquence</option>
                    <option value="calculation_mismatch">Incohérence arithmétique</option>
                    <option value="suspicious_cancellation">Annulation suspecte</option>
                </select>
            </div>
            <div>
                <label style="font-size:11px; color:var(--muted); display:block; margin-bottom:4px;">Statut</label>
                <select wire:model.live="resolved" style="background:var(--surface-3); border:1px solid var(--border); color:var(--text); padding:8px 12px; border-radius:8px; font-size:13px;">
                    <option value="0">Non résolues</option>
                    <option value="1">Résolues</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Sévérité</th>
                        <th>Type</th>
                        <th>Journal</th>
                        <th>Facture</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($anomalies as $anomaly)
                    <tr>
                        <td>
                            <span class="badge badge-{{ $anomaly->severity }}">
                                {{ match($anomaly->severity) {
                                    'critical' => '🔴 Critique',
                                    'warning'  => '🟡 Avert.',
                                    default    => '🔵 Info',
                                } }}
                            </span>
                        </td>
                        <td style="font-size:12px;">{{ str_replace('_',' ', ucfirst($anomaly->type)) }}</td>
                        <td style="font-size:12px; color:var(--muted);">{{ $anomaly->journal?->original_name ?? '—' }}</td>
                        <td style="font-size:12px;">{{ $anomaly->invoice?->invoice_no ?? '—' }}</td>
                        <td style="font-size:12px; max-width:300px;">{{ Str::limit($anomaly->description, 80) }}</td>
                        <td style="font-size:11px; color:var(--muted);">{{ $anomaly->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if(!$anomaly->is_resolved)
                            <button wire:click="resolve({{ $anomaly->id }})"
                                class="btn btn-ghost btn-sm"
                                title="Marquer comme résolu">✓</button>
                            @else
                            <span style="color:#10B981; font-size:12px;">✓ Résolu</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center; color:var(--muted); padding:32px;">
                            Aucune anomalie trouvée pour ces critères.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:16px;">
            {{ $anomalies->links() }}
        </div>
    </div>
</div>
