@section('title', 'Transactions')

<div>
    {{-- Filters & Exports Bar --}}
    <div class="card" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 16px; flex-wrap: wrap;">
            
            {{-- Search and Filters --}}
            <div style="display: flex; gap: 12px; flex-wrap: wrap; flex: 1;">
                <div style="min-width: 240px; flex: 1;">
                    <label style="display: block; font-size: 11px; font-weight: 600; color: var(--muted); margin-bottom: 6px; text-transform: uppercase;">Recherche</label>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="N° facture, nom client..." style="width: 100%; background: var(--surface-3); border: 1px solid var(--border); color: var(--text); padding: 8px 12px; border-radius: 8px; font-size: 13px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
                </div>
                
                <div style="width: 160px;">
                    <label style="display: block; font-size: 11px; font-weight: 600; color: var(--muted); margin-bottom: 6px; text-transform: uppercase;">Type Facture</label>
                    <select wire:model.live="type" style="width: 100%; background: var(--surface-3); border: 1px solid var(--border); color: var(--text); padding: 8px 12px; border-radius: 8px; font-size: 13px; outline: none;">
                        <option value="">Tous les types</option>
                        <option value="FACTURE">Facture (Vente)</option>
                        <option value="AVOIR">Note d'Avoir</option>
                        <option value="ANNULATION">Annulation</option>
                    </select>
                </div>

                <div style="min-width: 220px; flex: 1;">
                    <label style="display: block; font-size: 11px; font-weight: 600; color: var(--muted); margin-bottom: 6px; text-transform: uppercase;">Journal Source</label>
                    <select wire:model.live="journalId" style="width: 100%; background: var(--surface-3); border: 1px solid var(--border); color: var(--text); padding: 8px 12px; border-radius: 8px; font-size: 13px; outline: none;">
                        <option value="">Tous les journaux</option>
                        @foreach($journals as $journal)
                            <option value="{{ $journal->id }}">
                                {{ $journal->filename }} ({{ $journal->device->nid ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Export Buttons --}}
            @if($journalId)
            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                <a href="{{ route('export.invoices.excel', ['journal' => $journalId, 'type' => $type ?: 'all']) }}" class="btn btn-ghost">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px; height:16px; margin-right:4px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Exporter Excel
                </a>
            </div>
            @endif

        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="cursor: pointer;" wire:click="sort('invoice_no')">
                            N° Facture
                            @if($sortBy === 'invoice_no')
                                <span>{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </th>
                        <th>Type</th>
                        <th style="cursor: pointer;" wire:click="sort('date_time')">
                            Date / Heure
                            @if($sortBy === 'date_time')
                                <span>{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </th>
                        <th>Client</th>
                        <th style="text-align: right; cursor: pointer;" wire:click="sort('total_ht')">
                            Total HT (USD)
                            @if($sortBy === 'total_ht')
                                <span>{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </th>
                        <th style="text-align: right;">TVA (USD)</th>
                        <th style="text-align: right; cursor: pointer;" wire:click="sort('total_ttc')">
                            Total TTC (USD)
                            @if($sortBy === 'total_ttc')
                                <span>{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </th>
                        <th>Matériel (NID)</th>
                        <th>Journal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td style="font-weight: 600; color: var(--text);">
                                {{ $invoice->invoice_no }}
                            </td>
                            <td>
                                @if($invoice->type === 'FACTURE')
                                    <span class="badge badge-sale">Vente</span>
                                @elseif($invoice->type === 'AVOIR')
                                    <span class="badge badge-credit">Avoir</span>
                                @else
                                    <span class="badge badge-cancelled">Annulé</span>
                                @endif
                            </td>
                            <td style="color: var(--muted);">
                                {{ $invoice->date_time ? \Carbon\Carbon::parse($invoice->date_time)->format('d/m/Y H:i') : 'N/A' }}
                            </td>
                            <td>
                                <div style="font-weight: 500;">{{ $invoice->buyer_name ?? 'Client Anonyme' }}</div>
                                @if($invoice->buyer_nif)
                                    <div style="font-size: 10px; color: var(--muted); margin-top: 2px;">NIF: {{ $invoice->buyer_nif }}</div>
                                @endif
                            </td>
                            <td style="text-align: right; font-family: monospace;">
                                {{ number_format($invoice->total_ht, 2, ',', ' ') }}
                            </td>
                            <td style="text-align: right; font-family: monospace; color: var(--warning);">
                                {{ number_format($invoice->total_tva, 2, ',', ' ') }}
                            </td>
                            <td style="text-align: right; font-weight: 600; font-family: monospace; color: var(--success);">
                                {{ number_format($invoice->total_ttc, 2, ',', ' ') }}
                            </td>
                            <td style="font-size: 12px; color: var(--muted);">
                                {{ $invoice->journal->device->nid ?? 'N/A' }}
                            </td>
                            <td style="font-size: 12px;">
                                <span title="{{ $invoice->journal->filename }}" style="cursor: help; color: var(--accent);">
                                    {{ Str::limit($invoice->journal->filename, 15) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px 0; color: var(--muted);">
                                Aucune transaction trouvée pour ces filtres.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($invoices->hasPages())
            <div style="margin-top: 20px;">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div>
