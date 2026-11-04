@section('title', 'Transactions')

<div>
    {{-- Filters & Controls Bar --}}
    <div class="card" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 16px; flex-wrap: wrap;">
            
            {{-- Search and Filters Grid --}}
            <div style="display: flex; gap: 14px; flex-wrap: wrap; flex: 1;">
                {{-- Search Input --}}
                <div style="min-width: 220px; flex: 1.2;">
                    <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); margin-bottom: 7px; text-transform: uppercase; letter-spacing: 0.05em;">Recherche</label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <svg style="position: absolute; left: 12px; color: var(--text-muted); width: 16px; height: 16px; pointer-events: none;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="N° facture, client..." style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 9px 12px 9px 36px; border-radius: 9px; font-size: 13px; outline: none;">
                    </div>
                </div>
                
                {{-- Type Filter --}}
                <div style="width: 160px;">
                    <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); margin-bottom: 7px; text-transform: uppercase; letter-spacing: 0.05em;">Type Facture</label>
                    <select wire:model.live="type" style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 13px; outline: none;">
                        <option value="">Tous les types</option>
                        <option value="sale">Vente</option>
                        <option value="credit_note">Note d'Avoir</option>
                        <option value="cancelled">Annulation</option>
                    </select>
                </div>

                {{-- POS Filter --}}
                <div style="min-width: 180px; flex: 1;">
                    <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); margin-bottom: 7px; text-transform: uppercase; letter-spacing: 0.05em;">Point de Vente</label>
                    <select wire:model.live="posId" style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 13px; outline: none;">
                        <option value="">Tous les PDV</option>
                        @foreach($pointsOfSale as $pos)
                            <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Journal Filter --}}
                <div style="min-width: 200px; flex: 1;">
                    <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); margin-bottom: 7px; text-transform: uppercase; letter-spacing: 0.05em;">
                        Journal Source @if($posId) <span style="color: var(--primary); font-size: 10px;">(filtré)</span>@endif
                    </label>
                    <select wire:model.live="journalId" style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 13px; outline: none;">
                        <option value="">Tous les journaux</option>
                        @foreach($journals as $journal)
                            <option value="{{ $journal->id }}">
                                {{ $journal->filename }} — {{ $journal->device->pointOfSale->name ?? ($journal->device->nid ?? 'N/A') }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Export Button --}}
            @if($journalId)
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('export.invoices.excel', ['journal' => $journalId, 'type' => $type ?: 'all']) }}" class="btn btn-ghost">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Exporter Excel</span>
                </a>
            </div>
            @endif

        </div>
    </div>

    {{-- Transactions Table Card --}}
    <div class="card" style="padding: 0; overflow: hidden;">
        {{-- Results summary bar --}}
        <div style="padding: 16px 24px; border-bottom: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center; background: #F8FAFC;">
            <div style="font-size: 13px; color: var(--text-secondary); display: flex; align-items: center; gap: 8px;">
                <span style="font-weight: 700; color: var(--text-main);" class="tabular-nums">{{ number_format($invoices->total()) }}</span>
                <span>transaction(s) répertoriée(s)</span>
                @if($type)
                    <span style="color: var(--text-muted);">·</span>
                    <span class="badge badge-neutral">
                        Filtre : {{ match($type) { 'sale' => 'Ventes', 'credit_note' => 'Avoirs', 'cancelled' => 'Annulations', default => $type } }}
                    </span>
                @endif
            </div>
            <div style="font-size: 12px; color: var(--text-muted); font-weight: 500;" class="tabular-nums">
                Page {{ $invoices->currentPage() }} sur {{ $invoices->lastPage() }}
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="cursor: pointer; user-select: none;" wire:click="sort('invoice_no')">
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <span>N° Facture</span>
                                @if($sortBy === 'invoice_no')
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortDir === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th>Type</th>
                        <th style="cursor: pointer; user-select: none;" wire:click="sort('date_time')">
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <span>Date / Heure</span>
                                @if($sortBy === 'date_time')
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortDir === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th>Client</th>
                        <th style="text-align: right; cursor: pointer; user-select: none;" wire:click="sort('total_ht')">
                            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 6px;">
                                <span>Total HT (CDF)</span>
                                @if($sortBy === 'total_ht')
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortDir === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th style="text-align: right;">TVA (CDF)</th>
                        <th style="text-align: right; cursor: pointer; user-select: none;" wire:click="sort('total_ttc')">
                            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 6px;">
                                <span>Total TTC (CDF)</span>
                                @if($sortBy === 'total_ttc')
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortDir === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th>Matériel (NID)</th>
                        <th>Journal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td style="font-weight: 600; color: var(--text-main); font-family: monospace;">
                                {{ $invoice->invoice_no }}
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($invoice->type) {
                                        'sale'        => 'badge-sale',
                                        'credit_note' => 'badge-credit',
                                        'cancelled'   => 'badge-cancelled',
                                        default       => 'badge-neutral',
                                    };
                                    $badgeLabel = match($invoice->type) {
                                        'sale'        => 'Vente',
                                        'credit_note' => 'Avoir',
                                        'cancelled'   => 'Annulée',
                                        default       => $invoice->type,
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">
                                    <span class="status-dot"></span>
                                    {{ $badgeLabel }}
                                </span>
                            </td>
                            <td style="color: var(--text-secondary); font-size: 12.5px;">
                                {{ $invoice->date_time ? \Carbon\Carbon::parse($invoice->date_time)->format('d/m/Y H:i') : 'N/A' }}
                            </td>
                            <td>
                                <div style="font-weight: 500; color: var(--text-main);">{{ $invoice->buyer_name ?? 'Client Anonyme' }}</div>
                                @if($invoice->buyer_id)
                                    <div style="font-size: 11px; color: var(--text-muted); font-family: monospace; margin-top: 2px;">ID: {{ $invoice->buyer_id }}</div>
                                @endif
                            </td>
                            <td style="text-align: right; font-family: monospace;" class="tabular-nums">
                                {{ number_format($invoice->total_ht, 2, ',', ' ') }}
                            </td>
                            <td style="text-align: right; font-family: monospace; color: #D97706; font-weight: 600;" class="tabular-nums">
                                {{ number_format($invoice->total_tva, 2, ',', ' ') }}
                            </td>
                            <td style="text-align: right; font-weight: 700; font-family: monospace; color: #059669;" class="tabular-nums">
                                {{ number_format($invoice->total_ttc, 2, ',', ' ') }}
                            </td>
                            <td style="font-size: 12px; font-family: monospace; color: var(--text-muted);">
                                {{ $invoice->journal->device->nid ?? 'N/A' }}
                            </td>
                            <td style="font-size: 12px;">
                                <span title="{{ $invoice->journal->filename }}" style="cursor: help; color: var(--primary); font-family: monospace;">
                                    {{ Str::limit($invoice->journal->filename, 16) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 50px 20px; color: var(--text-muted);">
                                Aucune transaction trouvée correspondant à vos critères.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($invoices->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid var(--border-subtle); background: #F8FAFC;">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div>
