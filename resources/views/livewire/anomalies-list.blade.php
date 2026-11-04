@section('title', 'Anomalies')

<div>
    {{-- Filters Card --}}
    <div class="card" style="margin-bottom: 24px;">
        <div style="display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 200px;">
                <label style="font-size: 11px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 7px; text-transform: uppercase; letter-spacing: 0.05em;">Journal Source</label>
                <select wire:model.live="journalId" style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 13px; outline: none;">
                    <option value="">Tous les journaux</option>
                    @foreach($journals as $j)
                        <option value="{{ $j->id }}">{{ $j->original_name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="min-width: 160px;">
                <label style="font-size: 11px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 7px; text-transform: uppercase; letter-spacing: 0.05em;">Sévérité</label>
                <select wire:model.live="severity" style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 13px; outline: none;">
                    <option value="">Toutes les sévérités</option>
                    <option value="critical">Critique</option>
                    <option value="warning">Avertissement</option>
                    <option value="info">Information</option>
                </select>
            </div>

            <div style="min-width: 200px;">
                <label style="font-size: 11px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 7px; text-transform: uppercase; letter-spacing: 0.05em;">Type d'Anomalie</label>
                <select wire:model.live="type" style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 13px; outline: none;">
                    <option value="">Tous les types</option>
                    <option value="mcf_error">Erreur MCF</option>
                    <option value="gap_sequence">Rupture de séquence</option>
                    <option value="calculation_mismatch">Incohérence arithmétique</option>
                    <option value="suspicious_cancellation">Annulation suspecte</option>
                </select>
            </div>

            <div style="min-width: 150px;">
                <label style="font-size: 11px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 7px; text-transform: uppercase; letter-spacing: 0.05em;">Statut</label>
                <select wire:model.live="resolved" style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 13px; outline: none;">
                    <option value="0">Non résolues</option>
                    <option value="1">Résolues</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Anomalies Table Card --}}
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Sévérité</th>
                        <th>Type</th>
                        <th>Journal</th>
                        <th>Facture</th>
                        <th>Description</th>
                        <th>Date Détection</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($anomalies as $anomaly)
                    <tr>
                        <td>
                            @php
                                $sevClass = match($anomaly->severity) {
                                    'critical' => 'badge-critical',
                                    'warning'  => 'badge-warning',
                                    default    => 'badge-info',
                                };
                                $sevLabel = match($anomaly->severity) {
                                    'critical' => 'Critique',
                                    'warning'  => 'Avertissement',
                                    default    => 'Info',
                                };
                            @endphp
                            <span class="badge {{ $sevClass }}">
                                <span class="status-dot"></span>
                                {{ $sevLabel }}
                            </span>
                        </td>
                        <td style="font-size: 12.5px; font-weight: 500; color: var(--text-main);">
                            {{ str_replace('_', ' ', ucfirst($anomaly->type)) }}
                        </td>
                        <td style="font-size: 12px; font-family: monospace; color: var(--text-secondary);">
                            {{ $anomaly->journal?->original_name ?? '—' }}
                        </td>
                        <td style="font-size: 12.5px; font-family: monospace; font-weight: 600; color: var(--primary);">
                            {{ $anomaly->invoice?->invoice_no ?? '—' }}
                        </td>
                        <td style="font-size: 12.5px; color: var(--text-secondary); max-width: 320px; line-height: 1.4;">
                            {{ $anomaly->description }}
                        </td>
                        <td style="font-size: 12px; color: var(--text-muted); white-space: nowrap;">
                            {{ $anomaly->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td style="text-align: right;">
                            @if(!$anomaly->is_resolved)
                                <button wire:click="resolve({{ $anomaly->id }})"
                                    class="btn btn-ghost btn-sm"
                                    title="Marquer comme résolu"
                                    style="color: #059669; border-color: #A7F3D0; background: #ECFDF5;">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>Résoudre</span>
                                </button>
                            @else
                                <span class="badge badge-sale">
                                    <span class="status-dot"></span>
                                    Résolu
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 48px 20px;">
                            Aucune anomalie détectée pour ces filtres d'audit.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($anomalies->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid var(--border-subtle); background: #F8FAFC;">
                {{ $anomalies->links() }}
            </div>
        @endif
    </div>
</div>
