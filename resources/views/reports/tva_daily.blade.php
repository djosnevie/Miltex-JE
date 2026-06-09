<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a2e; padding: 20px; }
    .section { padding: 12px 0; }
    .section-title { font-size: 11px; font-weight: bold; color: #0A0537; border-bottom: 2px solid #0000C8; padding-bottom: 4px; margin-bottom: 10px; text-transform: uppercase; }
    table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
    table.data-table th { background: #0A0537; color: white; padding: 7px 10px; text-align: left; font-size: 9px; font-weight: bold; }
    table.data-table td { padding: 6px 10px; font-size: 9px; border-bottom: 1px solid #eee; color: #333; }
    table.data-table tr:nth-child(even) td { background: #f8fafc; }
    .total-row td { font-weight: bold; background: #e0e7ff !important; border-top: 2px solid #0000C8; color: #0A0537; }
    .footer { position: fixed; bottom: 10px; left: 20px; right: 20px; text-align: center; font-size: 8px; color: #aaa; border-top: 1px solid #eee; padding-top: 8px; }
</style>
</head>
<body>

{{-- Brand Header Table --}}
<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; border-bottom: 3px solid #0000C8;">
    <tr>
        <td style="width: 40%; padding-bottom: 10px; vertical-align: middle;">
            @if(file_exists(storage_path('app/public/logo.png')))
                <img src="{{ storage_path('app/public/logo.png') }}" style="height: 48px; display: block;" alt="Miltex Group">
            @else
                <span style="font-size: 16px; font-weight: bold; color: #0A0537;">⚡ Miltex Group</span>
            @endif
        </td>
        <td style="width: 60%; text-align: right; padding-bottom: 10px; vertical-align: middle; line-height: 1.4; color: #0A0537;">
            <h1 style="font-size: 14px; font-weight: bold; margin-bottom: 4px; color: #0A0537; text-transform: uppercase; letter-spacing: 0.5px;">Rapport TVA Journalier</h1>
            <div style="font-size: 9px; font-weight: bold;">{{ $journal->device->pointOfSale->company->name ?? 'Miltex SARL' }}</div>
            <div style="font-size: 8px; color: #555;">NIF : {{ $journal->device->pointOfSale->company->nif ?? '—' }} | Dispositif : {{ $journal->device->nid }}</div>
            <div style="font-size: 8px; color: #777;">Généré le : {{ now()->format('d/m/Y H:i') }}</div>
        </td>
    </tr>
</table>

{{-- Identification --}}
<div class="section">
    <div class="section-title">Identification du Dispositif</div>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
        <tr>
            <td style="width: 50%; padding: 4px 0; border: none; font-size: 9px;"><span style="font-weight: bold; color: #555;">Point de vente :</span> {{ $journal->device->pointOfSale->name ?? '—' }}</td>
            <td style="width: 50%; padding: 4px 0; border: none; font-size: 9px;"><span style="font-weight: bold; color: #555;">Période :</span> {{ $journal->start_date?->format('d/m/Y') }} → {{ $journal->end_date?->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td style="width: 50%; padding: 4px 0; border: none; font-size: 9px;"><span style="font-weight: bold; color: #555;">Dispositif (NID) :</span> {{ $journal->device->nid }}</td>
            <td style="width: 50%; padding: 4px 0; border: none; font-size: 9px;"><span style="font-weight: bold; color: #555;">Fichier journal :</span> {{ $journal->original_name }}</td>
        </tr>
        <tr>
            <td style="width: 50%; padding: 4px 0; border: none; font-size: 9px;"><span style="font-weight: bold; color: #555;">ISF :</span> {{ $journal->device->isf ?? '—' }}</td>
            <td style="width: 50%; padding: 4px 0; border: none; font-size: 9px;">&nbsp;</td>
        </tr>
    </table>
</div>

{{-- Détail journalier --}}
<div class="section">
    <div class="section-title">Détail Journalier de la TVA Collectée</div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20%">Date</th>
                <th style="width: 15%; text-align: center">Nbr Ventes</th>
                <th style="width: 20%; text-align: right">Total HT (CDF)</th>
                <th style="width: 10%; text-align: center">Taux</th>
                <th style="width: 15%; text-align: right">TVA Collectée (CDF)</th>
                <th style="width: 20%; text-align: right">Total TTC (CDF)</th>
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
                    <td style="text-align: center;">{{ $day['count'] }}</td>
                    <td style="text-align: right; font-family: monospace;">{{ number_format($day['ht'], 2, ',', ' ') }}</td>
                    <td style="text-align: center;">16%</td>
                    <td style="text-align: right; font-family: monospace;">{{ number_format($day['tva'], 2, ',', ' ') }}</td>
                    <td style="text-align: right; font-family: monospace;">{{ number_format($day['ttc'], 2, ',', ' ') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: var(--muted);">Aucune transaction enregistrée.</td>
                </tr>
            @endforelse

            <tr class="total-row">
                <td>TOTAL GÉNÉRAL</td>
                <td style="text-align: center;">{{ $grandTotalCount }}</td>
                <td style="text-align: right; font-family: monospace;">{{ number_format($grandTotalHt, 2, ',', ' ') }}</td>
                <td style="text-align: center;">—</td>
                <td style="text-align: right; font-family: monospace;">{{ number_format($grandTotalTva, 2, ',', ' ') }}</td>
                <td style="text-align: right; font-family: monospace;">{{ number_format($grandTotalTtc, 2, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>
</div>



<div class="footer">
    Miltex EAJE — Système d'Analyse des Journaux Électroniques Fiscaux — Document généré automatiquement, non officiel sans signature
</div>

</body>
</html>
