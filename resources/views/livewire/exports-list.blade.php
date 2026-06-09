@section('title', 'Rapports & Exports')

<div>
    {{-- Journal Selector Card --}}
    <div class="card" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
            <div>
                <h3 style="margin: 0 0 6px 0; font-size: 15px; font-weight: 600;">Sélectionner le Journal Électronique</h3>
                <p style="margin: 0; font-size: 12px; color: var(--muted);">Choisissez le journal contenant les données à exporter</p>
            </div>
            
            <div style="min-width: 280px;">
                @if($journals->isNotEmpty())
                    <select wire:model.live="selectedJournalId" style="width: 100%; background: var(--surface-3); border: 1px solid var(--border); color: var(--text); padding: 10px 14px; border-radius: 8px; font-size: 13.5px; outline: none; cursor: pointer;">
                        @foreach($journals as $j)
                            <option value="{{ $j->id }}">
                                {{ $j->original_name }} ({{ $j->device->nid ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                @else
                    <span style="font-size: 13px; color: var(--muted);">Aucun journal disponible</span>
                @endif
            </div>
        </div>
    </div>

    @if(!$selectedJournal)
        <div class="alert alert-info" style="margin-top: 20px;">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Veuillez d'abord importer un journal pour accéder aux rapports et exports.
            <a href="{{ route('journals.import') }}" style="color: inherit; font-weight: 600; margin-left: 6px;">Importer un journal →</a>
        </div>
    @else
        {{-- Selected Journal Details --}}
        <div class="card" style="margin-bottom: 24px; padding: 16px 20px; border-left: 3px solid var(--accent);">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px;">
                <div>
                    <span style="font-size: 10px; font-weight: 600; text-transform: uppercase; color: var(--muted); display: block; margin-bottom: 4px;">Entreprise</span>
                    <span style="font-size: 13.5px; font-weight: 600; color: var(--text);">{{ $selectedJournal->device->pointOfSale->company->name ?? 'N/A' }}</span>
                    <span style="font-size: 11px; color: var(--muted); display: block; margin-top: 2px;">NIF: {{ $selectedJournal->device->pointOfSale->company->nif ?? 'N/A' }}</span>
                </div>
                <div>
                    <span style="font-size: 10px; font-weight: 600; text-transform: uppercase; color: var(--muted); display: block; margin-bottom: 4px;">Point de Vente</span>
                    <span style="font-size: 13.5px; font-weight: 500; color: var(--text);">{{ $selectedJournal->device->pointOfSale->name ?? 'N/A' }}</span>
                </div>
                <div>
                    <span style="font-size: 10px; font-weight: 600; text-transform: uppercase; color: var(--muted); display: block; margin-bottom: 4px;">Période du Journal</span>
                    <span style="font-size: 13.5px; font-weight: 500; color: var(--text);">
                        {{ $selectedJournal->start_date?->format('d/m/Y') ?? 'N/A' }} au {{ $selectedJournal->end_date?->format('d/m/Y') ?? 'N/A' }}
                    </span>
                </div>
                <div>
                    <span style="font-size: 10px; font-weight: 600; text-transform: uppercase; color: var(--muted); display: block; margin-bottom: 4px;">Périphérique DEF (NID)</span>
                    <span style="font-size: 13.5px; font-weight: 500; color: var(--text);">{{ $selectedJournal->device->nid ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        {{-- Export Cards Grid --}}
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; flex-wrap: wrap;">
            
            {{-- Card 1: Rapport Complet --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                        <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #34D399;">XLSX (Excel)</span>
                        <svg width="24" height="24" fill="none" stroke="var(--success)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 15px; font-weight: 600; color: var(--text);">Rapport Consolidé Complet</h4>
                    <p style="margin: 0 0 16px 0; font-size: 12.5px; color: var(--muted); line-height: 1.5;">
                        Rapport Excel complet et structuré en plusieurs feuilles de calcul. Contient la synthèse, le grand livre de vente, les articles vendus et le registre des anomalies fiscales.
                    </p>
                </div>
                <a href="{{ route('export.full.excel', $selectedJournal->id) }}" class="btn btn-success" style="width: 100%; justify-content: center;">
                    Télécharger le Rapport
                </a>
            </div>

            {{-- Card 2: Liste des Transactions --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                        <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #34D399;">XLSX (Excel)</span>
                        <svg width="24" height="24" fill="none" stroke="var(--success)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 15px; font-weight: 600; color: var(--text);">Journal des Transactions (Grand Livre)</h4>
                    <p style="margin: 0 0 16px 0; font-size: 12.5px; color: var(--muted); line-height: 1.5;">
                        Liste brute de l'ensemble des factures émises, avoirs et transactions annulées. Idéal pour intégrer les écritures dans votre logiciel de comptabilité interne.
                    </p>
                </div>
                <a href="{{ route('export.invoices.excel', [$selectedJournal->id, 'all']) }}" class="btn btn-ghost" style="width: 100%; justify-content: center; border-color: var(--success); color: #34D399;">
                    Télécharger les Transactions
                </a>
            </div>

            {{-- Card 3: Statistiques d'Articles --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                        <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #34D399;">XLSX (Excel)</span>
                        <svg width="24" height="24" fill="none" stroke="var(--success)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 15px; font-weight: 600; color: var(--text);">Palmarès des Ventes d'Articles</h4>
                    <p style="margin: 0 0 16px 0; font-size: 12.5px; color: var(--muted); line-height: 1.5;">
                        Synthèse statistique des ventes d'articles, regroupée par nom de produit avec les quantités totales vendues, le chiffre d'affaires HT et la TVA générée par article.
                    </p>
                </div>
                <a href="{{ route('export.articles.excel', $selectedJournal->id) }}" class="btn btn-ghost" style="width: 100%; justify-content: center; border-color: var(--success); color: #34D399;">
                    Télécharger les Articles
                </a>
            </div>

            {{-- Card 4: Détail TVA Excel --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                        <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #34D399;">XLSX (Excel)</span>
                        <svg width="24" height="24" fill="none" stroke="var(--success)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 15px; font-weight: 600; color: var(--text);">Détail des Calculs de la TVA</h4>
                    <p style="margin: 0 0 16px 0; font-size: 12.5px; color: var(--muted); line-height: 1.5;">
                        Export Excel détaillé contenant la liste exhaustive des calculs de TVA ligne par ligne (Ventes et Avoirs) pour la réconciliation comptable et l'audit de la taxe due.
                    </p>
                </div>
                <a href="{{ route('export.tva.excel', $selectedJournal->id) }}" class="btn btn-ghost" style="width: 100%; justify-content: center; border-color: var(--success); color: #34D399;">
                    Télécharger le Grand Livre TVA
                </a>
            </div>

            {{-- Card 5: Synthèse de TVA --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                        <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #60A5FA;">PDF (Impression)</span>
                        <svg width="24" height="24" fill="none" stroke="var(--accent)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 15px; font-weight: 600; color: var(--text);">Synthèse Mensuelle de la TVA</h4>
                    <p style="margin: 0 0 16px 0; font-size: 12.5px; color: var(--muted); line-height: 1.5;">
                        Document PDF formateur officiel regroupant les totaux HT, TVA (16%) et TTC. Conçu spécifiquement pour servir de pièce justificative pour la déclaration mensuelle de TVA.
                    </p>
                </div>
                <a href="{{ route('export.tva.pdf', $selectedJournal->id) }}" class="btn btn-primary" style="width: 100%; justify-content: center;">
                    Télécharger la Synthèse TVA
                </a>
            </div>

            {{-- Card 6: TVA Journalière PDF --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                        <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #60A5FA;">PDF (Impression)</span>
                        <svg width="24" height="24" fill="none" stroke="var(--accent)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 15px; font-weight: 600; color: var(--text);">Rapport TVA Jour par Jour</h4>
                    <p style="margin: 0 0 16px 0; font-size: 12.5px; color: var(--muted); line-height: 1.5;">
                        Tableau récapitulatif de la taxe collectée ventilée par jour, indiquant le nombre de ventes quotidiennes et les bases imposables correspondantes.
                    </p>
                </div>
                <a href="{{ route('export.tva.daily.pdf', $selectedJournal->id) }}" class="btn btn-primary" style="width: 100%; justify-content: center;">
                    Télécharger la TVA Journalière
                </a>
            </div>

            {{-- Card 7: Audit & Conformité --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; grid-column: span 2;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                        <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #60A5FA;">PDF (Impression)</span>
                        <svg width="24" height="24" fill="none" stroke="var(--accent)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 15px; font-weight: 600; color: var(--text);">Rapport d'Audit de Conformité DGI</h4>
                    <p style="margin: 0 0 16px 0; font-size: 12.5px; color: var(--muted); line-height: 1.5;">
                        Rapport recensant l'état des dispositifs, les statistiques de facturation et l'intégralité des anomalies détectées (sauts de séquence, erreurs MCF, incohérences fiscales). Destiné à être présenté lors de contrôles ou audits fiscaux.
                    </p>
                </div>
                <a href="{{ route('export.compliance.pdf', $selectedJournal->id) }}" class="btn btn-primary" style="width: 100%; justify-content: center;">
                    Télécharger le Rapport d'Audit (PDF)
                </a>
            </div>

        </div>
    @endif
</div>
