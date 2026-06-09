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
    .badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 8px; font-weight: bold; }
    .badge-critical { background: #fee2e2; color: #991b1b; }
    .badge-warning  { background: #fef9c3; color: #92400e; }
    .badge-info     { background: #dbeafe; color: #1e40af; }
    .footer { position: fixed; bottom: 10px; left: 20px; right: 20px; text-align: center; font-size: 8px; color: #aaa; border-top: 1px solid #eee; padding-top: 8px; }
    .no-anomaly { text-align: center; color: #16a34a; font-weight: bold; padding: 20px; border: 1px dashed #16a34a; border-radius: 6px; background: #f0fdf4; }
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
            <h1 style="font-size: 14px; font-weight: bold; margin-bottom: 4px; color: #0A0537; text-transform: uppercase; letter-spacing: 0.5px;">Rapport de Conformité DGI</h1>
            <div style="font-size: 9px; font-weight: bold;">{{ $journal->device->pointOfSale->company->name ?? 'Miltex SARL' }}</div>
            <div style="font-size: 8px; color: #555;">NIF : {{ $journal->device->pointOfSale->company->nif ?? '—' }} | Dispositif : {{ $journal->device->nid }}</div>
            <div style="font-size: 8px; color: #777;">Généré le : {{ now()->format('d/m/Y H:i') }}</div>
        </td>
    </tr>
</table>

{{-- Section info --}}
<div class="section">
    <div class="section-title">Informations du Journal Électronique</div>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
        <tr>
            <td style="width: 50%; padding: 4px 0; border: none; font-size: 9px;"><span style="font-weight: bold; color: #555;">Fichier :</span> {{ $journal->original_name }}</td>
            <td style="width: 50%; padding: 4px 0; border: none; font-size: 9px;"><span style="font-weight: bold; color: #555;">Point de vente :</span> {{ $journal->device->pointOfSale->name ?? '—' }}</td>
        </tr>
        <tr>
            <td style="width: 50%; padding: 4px 0; border: none; font-size: 9px;"><span style="font-weight: bold; color: #555;">Période :</span> {{ $journal->start_date?->format('d/m/Y') }} → {{ $journal->end_date?->format('d/m/Y') }}</td>
            <td style="width: 50%; padding: 4px 0; border: none; font-size: 9px;"><span style="font-weight: bold; color: #555;">ISF :</span> {{ $journal->device->isf ?? '—' }}</td>
        </tr>
    </table>
</div>

{{-- Section KPI --}}
<div class="section">
    <div class="section-title">Résumé Financier</div>
    <table style="width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-left: -8px; margin-right: -8px;">
        <tr>
            <td style="width: 20%; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px; text-align: center; background: #fafafa;">
                <div style="font-size: 14px; font-weight: bold; color: #0000C8;">{{ number_format($journal->total_invoices, 0, ',', ' ') }}</div>
                <div style="font-size: 8px; color: #777; margin-top: 3px; font-weight: bold; text-transform: uppercase;">Ventes</div>
            </td>
            <td style="width: 25%; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px; text-align: center; background: #fafafa;">
                <div style="font-size: 14px; font-weight: bold; color: #0000C8;">{{ number_format($journal->total_ttc, 0, ',', ' ') }} CDF</div>
                <div style="font-size: 8px; color: #777; margin-top: 3px; font-weight: bold; text-transform: uppercase;">CA TTC</div>
            </td>
            <td style="width: 25%; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px; text-align: center; background: #fafafa;">
                <div style="font-size: 14px; font-weight: bold; color: #0000C8;">{{ number_format($journal->total_tva, 0, ',', ' ') }} CDF</div>
                <div style="font-size: 8px; color: #777; margin-top: 3px; font-weight: bold; text-transform: uppercase;">TVA (16%)</div>
            </td>
            <td style="width: 15%; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px; text-align: center; background: #fafafa;">
                <div style="font-size: 14px; font-weight: bold; color: #0000C8;">{{ $journal->total_cancelled }}</div>
                <div style="font-size: 8px; color: #777; margin-top: 3px; font-weight: bold; text-transform: uppercase;">Annulées</div>
            </td>
            <td style="width: 15%; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px; text-align: center; background: #fafafa;">
                <div style="font-size: 14px; font-weight: bold; color: #0000C8;">{{ $journal->total_credits }}</div>
                <div style="font-size: 8px; color: #777; margin-top: 3px; font-weight: bold; text-transform: uppercase;">Avoirs</div>
            </td>
        </tr>
    </table>
</div>

{{-- Section Anomalies --}}
<div class="section" style="margin-top: 10px;">
    <div class="section-title">Anomalies Détectées ({{ $journal->anomalies->count() }})</div>

    @if($journal->anomalies->isEmpty())
        <div class="no-anomaly">✔ Aucune anomalie détectée — Journal entièrement conforme aux exigences DGI</div>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 15%">Sévérité</th>
                    <th style="width: 20%">Type</th>
                    <th style="width: 15%">Facture</th>
                    <th style="width: 40%">Description</th>
                    <th style="width: 10%">Résolu</th>
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
                    <td style="font-weight: 500;">{{ $anomaly->type }}</td>
                    <td>{{ $anomaly->invoice?->invoice_no ?? '—' }}</td>
                    <td style="color: #444; line-height: 1.3;">{{ $anomaly->description }}</td>
                    <td style="font-weight: bold; color: {{ $anomaly->is_resolved ? '#16a34a' : '#ef4444' }};">
                        {{ $anomaly->is_resolved ? 'Oui' : 'Non' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<div class="footer">
    Rapport de Conformité DGI généré par Miltex EAJE — Système d'Analyse des Journaux Électroniques Fiscaux (RDC)
</div>

</body>
</html>
