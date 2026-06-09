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
    .highlight { background: #f0fdf4; border: 1px solid #0D6E3C; border-radius: 6px; padding: 10px 16px; margin-bottom: 16px; }
    .highlight .amount { font-size: 18px; font-weight: bold; color: #0D6E3C; }
</style>
</head>
<body>

<div class="header">
    <h1>Synthèse Mensuelle de la TVA</h1>
    <div class="sub">
        {{ $journal->device->pointOfSale->company->name ?? 'Miltex SARL' }} —
        NIF : {{ $journal->device->pointOfSale->company->nif ?? '—' }} —
        Généré le {{ now()->format('d/m/Y H:i') }}
    </div>
</div>

<div class="section">
    <div class="section-title">Identification</div>
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
    <div class="section-title">Récapitulatif de la Déclaration TVA</div>

    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th style="text-align:right">Montant (CDF)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Chiffre d'affaires brut (TTC)</td>
                <td style="text-align:right">{{ number_format($journal->total_ttc, 2, ',', ' ') }}</td>
            </tr>
            <tr>
                <td>Montant hors taxes (HT)</td>
                <td style="text-align:right">{{ number_format($journal->total_ht, 2, ',', ' ') }}</td>
            </tr>
            <tr>
                <td>Avoirs & remises accordés</td>
                <td style="text-align:right">—</td>
            </tr>
            <tr class="total-row">
                <td>TVA collectée à reverser à la DGI (16%)</td>
                <td style="text-align:right">{{ number_format($journal->total_tva, 2, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="highlight">
        <div style="font-size:9px; color:#555;">Montant total de TVA à déclarer</div>
        <div class="amount">{{ number_format($journal->total_tva, 2, ',', ' ') }} CDF</div>
    </div>
</div>

<div class="section">
    <div class="section-title">Détail des Opérations</div>
    <table>
        <thead>
            <tr>
                <th>Indicateur</th>
                <th style="text-align:center">Nombre</th>
                <th style="text-align:right">Montant TTC (CDF)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Factures de vente</td>
                <td style="text-align:center">{{ $journal->total_invoices }}</td>
                <td style="text-align:right">{{ number_format($journal->total_ttc, 2, ',', ' ') }}</td>
            </tr>
            <tr>
                <td>Avoirs / Notes de crédit</td>
                <td style="text-align:center">{{ $journal->total_credits }}</td>
                <td style="text-align:right">—</td>
            </tr>
            <tr>
                <td>Factures annulées</td>
                <td style="text-align:center">{{ $journal->total_cancelled }}</td>
                <td style="text-align:right">0,00</td>
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
