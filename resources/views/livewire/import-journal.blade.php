@section('title', 'Importer un Journal Électronique')

<div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px; align-items:start;">

        {{-- Upload Form --}}
        <div class="card">
            <div class="card-title">Nouveau Journal</div>

            {{-- Results --}}
            @foreach($results as $result)
                <div class="alert alert-{{ $result['status'] === 'success' ? 'success' : ($result['status'] === 'warning' ? 'info' : 'error') }}" style="margin-bottom:10px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($result['status'] === 'success')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        @endif
                    </svg>
                    <div>
                        <strong>{{ $result['name'] }}</strong><br>
                        <span style="font-size:12px;">{{ $result['message'] }}</span>
                    </div>
                </div>
            @endforeach

            {{-- Device Selector --}}
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; color:var(--muted); margin-bottom:6px; font-weight:600; text-transform:uppercase; letter-spacing:.4px;">
                    Dispositif Fiscal (DEF)
                </label>
                <select wire:model="deviceId"
                    style="width:100%; background:var(--surface-3); border:1px solid var(--border); color:var(--text); padding:10px 12px; border-radius:9px; font-size:13px; outline:none;">
                    <option value="">-- Sélectionner un DEF --</option>
                    @foreach($devices as $device)
                        <option value="{{ $device->id }}">
                            {{ $device->nid }} — {{ $device->pointOfSale?->name }} ({{ $device->model ?? 'DEF' }})
                        </option>
                    @endforeach
                </select>
                @error('deviceId') <span style="color:#F87171; font-size:11px; margin-top:4px; display:block;">{{ $message }}</span> @enderror

                @if($devices->isEmpty())
                    <div class="alert alert-info" style="margin-top:10px; font-size:12px;">
                        Aucun dispositif enregistré. Configurez d'abord votre entreprise et votre DEF.
                    </div>
                @endif
            </div>

            {{-- Upload Zone --}}
            <div
                class="upload-zone"
                x-data="{ dragover: false }"
                @dragover.prevent="dragover = true"
                @dragleave.prevent="dragover = false"
                @drop.prevent="dragover = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                :class="{ 'dragover': dragover }"
                @click="$refs.fileInput.click()"
            >
                <svg width="40" height="40" fill="none" stroke="var(--muted)" viewBox="0 0 24 24" style="margin:0 auto 12px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                <p style="font-size:14px; font-weight:600; margin-bottom:4px;">Glisser-déposer vos fichiers ici</p>
                <p style="font-size:12px; color:var(--muted);">ou cliquez pour parcourir — Format : .txt (CP1252)</p>
                <input
                    x-ref="fileInput"
                    wire:model="files"
                    type="file"
                    accept=".txt"
                    multiple
                    style="display:none;"
                >
            </div>

            @error('files.*') <span style="color:#F87171; font-size:11px; margin-top:4px; display:block;">{{ $message }}</span> @enderror

            {{-- Selected files preview --}}
            @if(!empty($files))
            <div style="margin-top:12px; display:flex; flex-direction:column; gap:6px;">
                @foreach($files as $file)
                <div style="display:flex; align-items:center; gap:8px; background:var(--surface-3); padding:8px 12px; border-radius:8px; font-size:12px;">
                    <svg width="16" height="16" fill="none" stroke="#3B82F6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>{{ $file->getClientOriginalName() }}</span>
                    <span style="margin-left:auto; color:var(--muted);">{{ number_format($file->getSize() / 1024, 1) }} Ko</span>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Loading bar --}}
            @if($importing)
            <div class="progress-bar" style="margin-top:16px;">
                <div class="progress-fill" style="width:100%; animation: pulse 1.5s ease-in-out infinite;"></div>
            </div>
            <p style="font-size:12px; color:var(--muted); text-align:center; margin-top:8px;">Parsing en cours…</p>
            @endif

            {{-- Submit --}}
            <button
                wire:click="import"
                wire:loading.attr="disabled"
                class="btn btn-primary"
                style="width:100%; margin-top:16px; justify-content:center;"
                @if(empty($files) || !$deviceId) disabled @endif
            >
                <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span wire:loading.remove>Importer et Analyser</span>
                <span wire:loading>Traitement…</span>
            </button>
        </div>

        {{-- Recent Journals --}}
        <div class="card">
            <div class="card-title">Journaux Récents</div>
            @if($recentJournals->isEmpty())
                <div style="text-align:center; color:var(--muted); padding:32px; font-size:13px;">Aucun journal importé</div>
            @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Fichier</th>
                            <th>Point de vente</th>
                            <th style="text-align:right">CA TTC</th>
                            <th>Anomalies</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentJournals as $j)
                        <tr>
                            <td>
                                <div style="font-size:12px; font-weight:600;">{{ $j->original_name }}</div>
                                <div style="font-size:11px; color:var(--muted);">
                                    {{ $j->start_date?->format('d/m/Y') }} → {{ $j->end_date?->format('d/m/Y') }}
                                </div>
                            </td>
                            <td style="font-size:12px;">{{ $j->device?->pointOfSale?->name ?? '—' }}</td>
                            <td style="text-align:right; font-size:12px; font-weight:600;">
                                {{ number_format($j->total_ttc, 0, ',', ' ') }}
                            </td>
                            <td>
                                @php $cnt = $j->anomalies()->where('is_resolved', false)->count(); @endphp
                                @if($cnt > 0)
                                    <span class="badge badge-critical">{{ $cnt }}</span>
                                @else
                                    <span class="badge badge-sale">✓</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.5} }
</style>
