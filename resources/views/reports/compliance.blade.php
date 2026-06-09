<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a2e; }
    .header { background: #1E3A5F; color: white; padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; }
    .header h1 { font-size: 16px; font-weight: bold; }
    .header .meta { font-size: 9px; text-align: right; line-height: 1.6; }
    .section { padding: 16px 24px; }
    .section-title { font-size: 12px; font-weight: bold; color: #1E3A5F; border-bottom: 2px solid #1E3A5F; padding-bottom: 4px; margin-bottom: 10px; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px 20px; margin-bottom: 16px; }
    .info-row { display: flex; gap: 6px; }
    .info-label { font-weight: bold; color: #555; min-width: 130px; }
    .kpi-grid { display: flex; gap: 12px; margin-bottom: 16px; }
    .kpi-box { flex: 1; border: 1px solid #ddd; border-radius: 6px; padding: 10px 12px; text-align: center; }
    .kpi-box .value { font-size: 14px; font-weight: bold; color: #1E3A5F; }
    .kpi-box .label { font-size: 8px; color: #888; margin-top: 2px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
    th { background: #1E3A5F; color: white; padding: 6px 8px; text-align: left; font-size: 9px; }
    td { padding: 5px 8px; font-size: 9px; border-bottom: 1px solid #eee; }
    tr:nth-child(even) td { background: #f8f9fc; }
    .badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 8px; font-weight: bold; }
    .badge-critical { background: #fee2e2; color: #991b1b; }
    .badge-warning  { background: #fef9c3; color: #92400e; }
    .badge-info     { background: #dbeafe; color: #1e40af; }
    .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #aaa; border-top: 1px solid #eee; padding: 6px 0; }
    .no-anomaly { text-align: center; color: #16a34a; font-weight: bold; padding: 16px; }
</style>
</head>
<body>

<div class="header">
    <div>
        <h1>Rapport de Conformité DGI</h1>
        <div style="font-size:9px; margin-top:4px; opacity:.85;">{{ $journal->device->pointOfSale->company->name ?? 'Miltex SARL' }}</div>
    </div>
    <div class="meta">
        <div>NIF : {{ $journal->device->pointOfSale->company->nif ?? '—' }}</div>
        <div>Dispositif : {{ $journal->device->nid }}</div>
        <div>Généré le : {{ now()->format('d/m/Y H:i') }}</div>
    </div>
</div>

<div class="section">
    <div class="section-title">Informations du Journal Électronique</div>
    <div class="info-grid">
        <div class="info-row"><span class="info-label">Fichier :</span> {{ $journal->original_name }}</div>
        <div class="info-row"><span class="info-label">Point de vente :</span> {{ $journal->device->pointOfSale->name ?? '—' }}</div>
        <div class="info-row"><span class="info-label">Période :</span> {{ $journal->start_date?->format('d/m/Y') }} → {{ $journal->end_date?->format('d/m/Y') }}</div>
        <div class="info-row"><span class="info-label">ISF :</span> {{ $journal->device->isf ?? '—' }}</div>
    </div>
</div>

<div class="section">
    <div class="section-title">Résumé Financier</div>
    <div class="kpi-grid">
        <div class="kpi-box">
            <div class="value">{{ number_format($journal->total_invoices, 0, ',', ' ') }}</div>
            <div class="label">Factures de vente</div>
        </div>
        <div class="kpi-box">
            <div class="value">{{ number_format($journal->total_ttc, 0, ',', ' ') }} CDF</div>
            <div class="label">CA TTC</div>
        </div>
        <div class="kpi-box">
            <div class="value">{{ number_format($journal->total_tva, 0, ',', ' ') }} CDF</div>
            <div class="label">TVA collectée (16%)</div>
        </div>
        <div class="kpi-box">
            <div class="value">{{ $journal->total_cancelled }}</div>
            <div class="label">Annulations</div>
        </div>
        <div class="kpi-box">
            <div class="value">{{ $journal->total_credits }}</div>
            <div class="label">Avoirs / Crédits</div>
        </div>
    </div>
</div>

<div class="section">
    <div class="section-title">Anomalies Détectées ({{ $journal->anomalies->count() }})</div>

    @if($journal->anomalies->isEmpty())
        <div class="no-anomaly">✔ Aucune anomalie détectée — Journal conforme</div>
    @else
        <table>
            <thead>
                <tr>
                    <th>Sévérité</th>
                    <th>Type</th>
                    <th>Facture</th>
                    <th>Description</th>
                    <th>Résolu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($journal->anomalies as $anomaly)
                <tr>
                    <td>
                        <span class="badge badge-{{ $anomaly->severity }}">
                            {{ strtoupper($anomaly->severity) }}
                        </span>
                    </td>
                    <td>{{ $anomaly->type }}</td>
                    <td>{{ $anomaly->invoice?->invoice_no ?? '—' }}</td>
                    <td>{{ $anomaly->description }}</td>
                    <td>{{ $anomaly->is_resolved ? 'Oui' : 'Non' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<div class="footer">
    Rapport généré par Miltex EAJE — Système d'Analyse des Journaux Électroniques Fiscaux (RDC)
</div>

</body>
</html>
