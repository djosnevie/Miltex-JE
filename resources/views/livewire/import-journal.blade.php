@section('title', 'Importer un Journal Électronique')

<div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px; align-items: start;">

        {{-- Upload Form Card --}}
        <div class="card">
            <div class="card-title">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Importer de Nouveaux Fichiers
            </div>

            {{-- Results Banners --}}
            @foreach($results as $result)
                <div class="alert alert-{{ $result['status'] === 'success' ? 'success' : ($result['status'] === 'warning' ? 'warning' : 'error') }}" style="margin-bottom: 14px;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0;">
                        @if($result['status'] === 'success')
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        @endif
                    </svg>
                    <div>
                        <strong style="color: var(--text-main); font-size: 13.5px;">{{ $result['name'] }}</strong>
                        <div style="font-size: 12px; margin-top: 2px;">{{ $result['message'] }}</div>
                    </div>
                </div>
            @endforeach

            {{-- Device Selector --}}
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 11px; color: var(--text-muted); margin-bottom: 7px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                    Dispositif Électronique Fiscal (DEF)
                </label>
                <select wire:model="deviceId"
                    style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 10px 14px; border-radius: 10px; font-size: 13px; outline: none;">
                    <option value="">-- Sélectionner le DEF associé --</option>
                    @foreach($devices as $device)
                        <option value="{{ $device->id }}">
                            {{ $device->nid }} — {{ $device->pointOfSale?->name }} ({{ $device->model ?? 'DEF' }})
                        </option>
                    @endforeach
                </select>
                @error('deviceId') <span style="color: #DC2626; font-size: 12px; margin-top: 4px; display: block; font-weight: 500;">{{ $message }}</span> @enderror

                @if($devices->isEmpty())
                    <div class="alert alert-info" style="margin-top: 12px; font-size: 12.5px;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Aucun dispositif enregistré. Configurez d'abord un point de vente et un DEF dans la section Configuration.</span>
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
                <div style="width: 52px; height: 52px; border-radius: 14px; background: rgba(59, 130, 246, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; border: 1px solid rgba(59, 130, 246, 0.2);">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                </div>
                <p style="font-size: 14.5px; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">Glisser-déposer les fichiers journaux ici</p>
                <p style="font-size: 12.5px; color: var(--text-muted);">ou cliquez pour parcourir vos dossiers — Format .txt (Encodage CP1252)</p>
                <input
                    x-ref="fileInput"
                    wire:model="files"
                    type="file"
                    accept=".txt"
                    multiple
                    style="display: none;"
                >
            </div>

            @error('files.*') <span style="color: #DC2626; font-size: 12px; margin-top: 6px; display: block; font-weight: 500;">{{ $message }}</span> @enderror

            {{-- Selected files preview list --}}
            @if(!empty($files))
            <div style="margin-top: 16px; display: flex; flex-direction: column; gap: 8px;">
                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em;">Fichiers prêts pour l'analyse :</div>
                @foreach($files as $file)
                <div style="display: flex; align-items: center; gap: 10px; background: var(--surface-2); padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border-subtle); font-size: 13px;">
                    <svg width="18" height="18" fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span style="font-family: monospace; color: var(--text-main); font-weight: 500;">{{ $file->getClientOriginalName() }}</span>
                    <span style="margin-left: auto; color: var(--text-muted); font-size: 11.5px; font-family: monospace;" class="tabular-nums">{{ number_format($file->getSize() / 1024, 1) }} Ko</span>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Parsing progress indicator --}}
            @if($importing)
            <div class="progress-bar" style="margin-top: 20px;">
                <div class="progress-fill" style="width: 100%;"></div>
            </div>
            <p style="font-size: 12.5px; color: var(--text-secondary); text-align: center; margin-top: 10px;">Traitement et extraction fiscale en cours…</p>
            @endif

            {{-- Submit button --}}
            <button
                wire:click="import"
                wire:loading.attr="disabled"
                class="btn btn-primary"
                style="width: 100%; margin-top: 20px; padding: 12px; justify-content: center; font-size: 14px;"
                @if(empty($files) || !$deviceId) disabled @endif
            >
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                <span wire:loading.remove>Lancer l'Importation et l'Audit</span>
                <span wire:loading>Traitement fiscal en cours…</span>
            </button>
        </div>

        {{-- Recent Journals Card --}}
        <div class="card" style="padding: 0; overflow: hidden;">
            <div style="padding: 20px 24px; border-bottom: 1px solid var(--border-subtle);">
                <div class="card-title" style="margin: 0;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Derniers Journaux Importés
                </div>
            </div>

            @if($recentJournals->isEmpty())
                <div style="text-align: center; color: var(--text-muted); padding: 48px 20px; font-size: 13px;">
                    Aucun journal n'a encore été importé sur ce système.
                </div>
            @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Fichier Journal</th>
                            <th>Point de Vente</th>
                            <th style="text-align: right;">CA TTC (CDF)</th>
                            <th style="text-align: center;">Audit DGI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentJournals as $j)
                        <tr>
                            <td>
                                <div style="font-size: 13px; font-weight: 600; font-family: monospace; color: var(--text-main);">{{ $j->original_name }}</div>
                                <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">
                                    {{ $j->start_date?->format('d/m/Y') }} → {{ $j->end_date?->format('d/m/Y') }}
                                </div>
                            </td>
                            <td style="font-size: 12.5px; color: var(--text-secondary);">
                                {{ $j->device?->pointOfSale?->name ?? '—' }}
                            </td>
                            <td style="text-align: right; font-size: 13px; font-weight: 700; color: #059669; font-family: monospace;" class="tabular-nums">
                                {{ number_format($j->total_ttc, 0, ',', ' ') }}
                            </td>
                            <td style="text-align: center;">
                                @php $cnt = $j->anomalies()->where('is_resolved', false)->count(); @endphp
                                @if($cnt > 0)
                                    <span class="badge badge-critical">
                                        <span class="status-dot"></span>
                                        {{ $cnt }} anomalie(s)
                                    </span>
                                @else
                                    <span class="badge badge-sale">
                                        <span class="status-dot"></span>
                                        Conforme
                                    </span>
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
