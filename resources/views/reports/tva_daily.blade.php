<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a2e; }
    .header { background: #0D6E3C; color: white; padding: 20px 24px; }
    .header h1 { font-size: 16px; font-weight: bold; }
    .header .sub { font-size: 9px; margin-top: 4px; opacity: .85; }
    .section { padding: 16px 24px; }
    .section-title { font-size: 12px; font-weight: bold; color: #0D6E3C; border-bottom: 2px solid #0D6E3C; padding-bottom: 4px; margin-bottom: 12px; }
    .info-grid { display: flex; gap: 20px; margin-bottom: 16px; }
    .info-row { display: flex; gap: 6px; }
    .info-label { font-weight: bold; color: #555; min-width: 120px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
    th { background: #0D6E3C; color: white; padding: 7px 10px; text-align: left; font-size: 9px; }
    td { padding: 6px 10px; font-size: 9px; border-bottom: 1px solid #eee; }
    tr:nth-child(even) td { background: #f0fdf4; }
    .total-row td { font-weight: bold; background: #dcfce7 !important; border-top: 2px solid #0D6E3C; }
    .signature-block { margin-top: 30px; display: flex; justify-content: space-between; }
    .signature-box { width: 45%; border-top: 1px solid #333; padding-top: 6px; font-size: 9px; }
    .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #aaa; border-top: 1px solid #eee; padding: 6px 0; }
</style>
</head>
<body>

<div class="header">
    <h1>Rapport TVA Journalier (Jour par Jour)</h1>
    <div class="sub">
        {{ $journal->device->pointOfSale->company->name ?? 'Miltex SARL' }} —
        NIF : {{ $journal->device->pointOfSale->company->nif ?? '—' }} —
        Généré le {{ now()->format('d/m/Y H:i') }}
    </div>
</div>

<div class="section">
    <div class="section-title">Identification du Dispositif</div>
    <div class="info-grid">
        <div>
            <div class="info-row"><span class="info-label">Point de vente :</span> {{ $journal->device->pointOfSale->name ?? '—' }}</div>
            <div class="info-row"><span class="info-label">Dispositif (NID) :</span> {{ $journal->device->nid }}</div>
            <div class="info-row"><span class="info-label">ISF :</span> {{ $journal->device->isf ?? '—' }}</div>
        </div>
        <div>
            <div class="info-row"><span class="info-label">Période :</span> {{ $journal->start_date?->format('d/m/Y') }} → {{ $journal->end_date?->format('d/m/Y') }}</div>
            <div class="info-row"><span class="info-label">Fichier journal :</span> {{ $journal->original_name }}</div>
        </div>
    </div>
</div>

<div class="section">
    <div class="section-title">Détail Journalier de la TVA Collectée</div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th style="text-align:center">Nbr Ventes</th>
                <th style="text-align:right">Total HT (CDF)</th>
                <th style="text-align:center">Taux</th>
                <th style="text-align:right">TVA Collectée (CDF)</th>
                <th style="text-align:right">Total TTC (CDF)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotalHt = 0;
                $grandTotalTva = 0;
                $grandTotalTtc = 0;
                $grandTotalCount = 0;
            @endphp
            @forelse($dailyData as $day)
                @php
                    $grandTotalHt += $day['ht'];
                    $grandTotalTva += $day['tva'];
                    $grandTotalTtc += $day['ttc'];
                    $grandTotalCount += $day['count'];
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($day['date'])->format('d/m/Y') }}</td>
                    <td style="text-align:center">{{ $day['count'] }}</td>
                    <td style="text-align:right">{{ number_format($day['ht'], 2, ',', ' ') }}</td>
                    <td style="text-align:center">16%</td>
                    <td style="text-align:right">{{ number_format($day['tva'], 2, ',', ' ') }}</td>
                    <td style="text-align:right">{{ number_format($day['ttc'], 2, ',', ' ') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding: 20px;">Aucune transaction enregistrée.</td>
                </tr>
            @endforelse

            <tr class="total-row">
                <td>TOTAL GÉNÉRAL</td>
                <td style="text-align:center">{{ $grandTotalCount }}</td>
                <td style="text-align:right">{{ number_format($grandTotalHt, 2, ',', ' ') }}</td>
                <td style="text-align:center">—</td>
                <td style="text-align:right">{{ number_format($grandTotalTva, 2, ',', ' ') }}</td>
                <td style="text-align:right">{{ number_format($grandTotalTtc, 2, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="section">
    <div class="signature-block">
        <div class="signature-box">
            <strong>Cachet & Signature du Responsable</strong><br>
            Nom : ________________________<br>
            Date : ________________________
        </div>
        <div class="signature-box" style="text-align:right;">
            <strong>Visa du Contrôleur DGI</strong><br>
            Nom : ________________________<br>
            Date : ________________________
        </div>
    </div>
</div>

<div class="footer">
    Miltex EAJE — Système d'Analyse des Journaux Électroniques Fiscaux — Document généré automatiquement, non officiel sans signature
</div>

</body>
</html>
