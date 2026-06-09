<?php

namespace App\Services;

use App\Models\Anomaly;
use App\Models\Device;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Journal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JournalParserService
{
    private array $blocks  = [];
    private string $content = '';

    // ──────────────────────────────────────────────────────────────────────
    // ENTRY POINT
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Parse a JE text file and persist everything in one transaction.
     * Supports any DEF brand whose receipt header can be configured.
     */
    public function parseFile(string $filePath, string $originalName, int $deviceId): Journal
    {
        $filename = basename($filePath);

        // Duplicate guard: return existing journal
        if ($existing = Journal::where('filename', $filename)->first()) {
            return $existing;
        }

        // Hash guard: detect same content under a different filename
        $fileHash = hash_file('sha256', $filePath);
        if ($existing = Journal::where('file_hash', $fileHash)->first()) {
            throw new \RuntimeException(
                "Ce fichier a déjà été importé sous le nom : {$existing->original_name}"
            );
        }

        // Read & convert encoding (DEF devices write CP1252)
        $raw = file_get_contents($filePath);
        if ($raw === false) {
            throw new \RuntimeException("Impossible de lire le fichier : $filePath");
        }
        $this->content = mb_convert_encoding($raw, 'UTF-8', 'CP1252');

        $this->splitIntoBlocks();

        if (empty($this->blocks)) {
            throw new \RuntimeException("Aucun bloc de transaction trouvé dans : $filePath");
        }

        $journal = null;

        DB::transaction(function () use ($filePath, $filename, $originalName, $deviceId, $fileHash, &$journal) {

            $journal = Journal::create([
                'device_id'     => $deviceId,
                'filename'      => $filename,
                'original_name' => $originalName,
                'file_size'     => filesize($filePath),
                'file_hash'     => $fileHash,
                'parsed_at'     => now(),
                'currency'      => 'CDF',
            ]);

            $invoices    = [];
            $creditNotes = [];
            $cancelled   = [];

            foreach ($this->blocks as $block) {
                $parsed = $this->parseBlock($block, $journal->id);
                if ($parsed) {
                    match ($parsed->type) {
                        'sale'        => $invoices[]    = $parsed,
                        'credit_note' => $creditNotes[] = $parsed,
                        'cancelled'   => $cancelled[]   = $parsed,
                        default       => null,
                    };
                }
            }

            // ── Fiscal summary ─────────────────────────────────────────────
            $allParsed = array_merge($invoices, $creditNotes, $cancelled);
            $dates     = collect($allParsed)->pluck('date_time')->filter()->sort();

            $journal->update([
                'start_date'      => $dates->first(),
                'end_date'        => $dates->last(),
                'isf_id'          => $this->extractIsf($allParsed),
                'total_invoices'  => count($invoices),
                'total_credits'   => count($creditNotes),
                'total_cancelled' => count($cancelled),
                'total_ttc'       => collect($invoices)->sum('total_ttc'),
                'total_ht'        => collect($invoices)->sum('total_ht'),
                'total_tva'       => collect($invoices)->sum('total_tva'),
            ]);

            // ── Anomaly detection ──────────────────────────────────────────
            $this->detectAnomalies($journal, array_merge($invoices, $creditNotes, $cancelled));
        });

        return $journal;
    }

    // ──────────────────────────────────────────────────────────────────────
    // BLOCK SPLITTER
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Split the raw journal into individual receipt blocks.
     * Each block starts with the spaced company name header line.
     */
    private function splitIntoBlocks(): void
    {
        $this->blocks  = [];
        $currentBlock  = [];

        // The header pattern is configurable via config('eaje.block_header')
        $headerPattern = config('eaje.block_header', 'M I L T E X   S A R L');

        foreach (explode("\n", $this->content) as $line) {
            $line = rtrim($line, "\r");

            if (str_contains($line, $headerPattern)) {
                if (!empty($currentBlock)) {
                    $this->blocks[] = $currentBlock;
                }
                $currentBlock = [];
            }
            $currentBlock[] = $line;
        }

        if (!empty($currentBlock)) {
            $this->blocks[] = $currentBlock;
        }
    }

    // ──────────────────────────────────────────────────────────────────────
    // BLOCK PARSER
    // ──────────────────────────────────────────────────────────────────────

    private function parseBlock(array $block, int $journalId): ?Invoice
    {
        $text = implode("\n", $block);
        $norm = $this->normalize($text);

        $isInvoice   = str_contains($norm, 'FACTUREDEVENTE');
        $isCreditNote = str_contains($norm, 'FACTUREDAVOIR');

        if (!$isInvoice && !$isCreditNote) {
            return null;
        }

        // Invoice number is mandatory
        $invoiceNo = null;
        if (preg_match('/FACTURE\s*No\s*:\s*([^\n\r]+)/i', $text, $m)) {
            $invoiceNo = trim($m[1]);
        }
        if (!$invoiceNo) {
            return null;
        }

        // ── Parse z_number & serial_number from "0014/26/000309" ──────────
        $zNumber      = 0;
        $serialNumber = 0;
        if (preg_match('/^(\d+)\/\d+\/(\d+)$/', trim($invoiceNo), $m)) {
            $zNumber      = (int) $m[1];
            $serialNumber = (int) $m[2];
        }

        // ── Transaction date ──────────────────────────────────────────────
        $dateTime = null;
        if (preg_match('/DEF Heure:\s*([^\n\r]+)/i', $text, $m)) {
            try {
                $dateTime = Carbon::createFromFormat('d/m/Y H:i:s', trim($m[1]));
            } catch (\Exception) {}
        }
        // Fallback for cancelled blocks that only carry a DATE line
        if (!$dateTime && preg_match('/DATE\s+(\d{2}\/\d{2}\/\d{4})\s+HEURE\s+(\d{2}:\d{2}:\d{2})/i', $text, $m)) {
            try {
                $dateTime = Carbon::createFromFormat('d/m/Y H:i:s', "{$m[1]} {$m[2]}");
            } catch (\Exception) {}
        }

        // ── Buyer info ────────────────────────────────────────────────────
        $buyerName = $this->extractField($text, "Nom de l'acheteur");
        $buyerId   = $this->extractField($text, "ID de l'acheteur");
        $buyerType = $this->extractField($text, "Type de l'acheteur");
        $vendeur   = $this->extractField($text, 'VENDEUR');

        // ── Fiscal codes ──────────────────────────────────────────────────
        $codeDef    = null;
        if (preg_match('/CODE DEF\/DGI\s*\n\s*([^\n\r]+)/i', $text, $m)) {
            $codeDef = trim($m[1]);
        }
        $compteur   = $this->extractField($text, 'DEF Compteurs');

        // ── MCF error ─────────────────────────────────────────────────────
        $hasMcfError  = str_contains($text, 'Erreur MCF');
        $mcfErrorMsg  = null;
        if ($hasMcfError && preg_match('/Erreur MCF:\s*([^\n\r]+)/i', $text, $m)) {
            $mcfErrorMsg = trim($m[1]);
        }

        // ── Original ref for credit notes ─────────────────────────────────
        $originalRef = null;
        if ($isCreditNote && preg_match('/Ref\. de facture originale:\s*\n?\s*([A-Z0-9\-]+)/i', $text, $m)) {
            $originalRef = trim($m[1]);
        }

        // ── Payment mode ──────────────────────────────────────────────────
        $paymentMode = $this->detectPaymentMode($text);

        // ── Totals ────────────────────────────────────────────────────────
        [$totalTtc, $currency] = $this->parseTotalTtc($block);
        [$totalHt, $totalTva]  = $this->parseTaxTotals($block);

        // ── Cancellation flag ─────────────────────────────────────────────
        $isCancelled = str_contains($this->normalize($text), 'ANNULE')
            || str_contains($this->normalize($text), 'ANNULEE');

        $type = match (true) {
            $isCreditNote => 'credit_note',
            $isCancelled  => 'cancelled',
            default       => 'sale',
        };

        // ── Persist ───────────────────────────────────────────────────────
        $invoice = Invoice::create([
            'journal_id'        => $journalId,
            'invoice_no'        => trim($invoiceNo),
            'serial_number'     => $serialNumber,
            'z_number'          => $zNumber,
            'date_time'         => $dateTime,
            'buyer_name'        => $buyerName,
            'buyer_id'          => $buyerId,
            'buyer_type'        => $buyerType,
            'vendeur'           => $vendeur,
            'total_ttc'         => $totalTtc  ?? 0,
            'total_ht'          => $totalHt   ?? 0,
            'total_tva'         => $totalTva  ?? 0,
            'currency'          => $currency  ?? 'CDF',
            'type'              => $type,
            'code_def'          => $codeDef,
            'compteur_brut'     => $compteur,
            'has_mcf_error'     => $hasMcfError,
            'mcf_error_message' => $mcfErrorMsg,
            'original_ref_code' => $originalRef,
            'payment_mode'      => $paymentMode,
            'raw_text'          => $text,
        ]);

        $this->parseAndSaveItems($block, $invoice->id);

        return $invoice;
    }

    // ──────────────────────────────────────────────────────────────────────
    // ANOMALY DETECTOR
    // ──────────────────────────────────────────────────────────────────────

    private function detectAnomalies(Journal $journal, array $allInvoices): void
    {
        $anomalies = [];

        // ── 1. MCF hardware errors ─────────────────────────────────────────
        foreach ($allInvoices as $invoice) {
            if ($invoice->has_mcf_error) {
                $anomalies[] = [
                    'journal_id'  => $journal->id,
                    'invoice_id'  => $invoice->id,
                    'severity'    => 'critical',
                    'type'        => 'mcf_error',
                    'description' => "Erreur MCF sur la facture {$invoice->invoice_no} : " . ($invoice->mcf_error_message ?? 'Inconnue'),
                    'is_resolved' => false,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }
        }

        // ── 2. Sequence gaps within each Z report ─────────────────────────
        $salesByZ = collect($allInvoices)
            ->where('type', 'sale')
            ->groupBy('z_number');

        foreach ($salesByZ as $zNum => $group) {
            $serials = $group->pluck('serial_number')->sort()->values();
            for ($i = 1; $i < $serials->count(); $i++) {
                $expected = $serials[$i - 1] + 1;
                if ($serials[$i] > $expected) {
                    $gap = $serials[$i] - $expected;
                    $anomalies[] = [
                        'journal_id'  => $journal->id,
                        'invoice_id'  => null,
                        'severity'    => 'critical',
                        'type'        => 'gap_sequence',
                        'description' => "Rupture de séquence dans le rapport Z{$zNum} : {$gap} facture(s) manquante(s) entre #{$serials[$i-1]} et #{$serials[$i]}.",
                        'is_resolved' => false,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                }
            }
        }

        // ── 3. Arithmetic inconsistency (HT * 1.16 ≠ TTC) ────────────────
        foreach ($allInvoices as $invoice) {
            if ($invoice->type !== 'sale' || $invoice->total_ttc == 0) {
                continue;
            }
            $computed = round((float) $invoice->total_ht * 1.16, 2);
            $diff     = abs($computed - (float) $invoice->total_ttc);
            if ($diff > 1) { // tolerance: 1 CDF
                $anomalies[] = [
                    'journal_id'  => $journal->id,
                    'invoice_id'  => $invoice->id,
                    'severity'    => 'warning',
                    'type'        => 'calculation_mismatch',
                    'description' => "Incohérence arithmétique sur {$invoice->invoice_no} : HT×1.16 = {$computed} ≠ TTC = {$invoice->total_ttc}.",
                    'is_resolved' => false,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }
        }

        // ── 4. Suspicious high-value cancellations ────────────────────────
        $avgTtc = collect($allInvoices)->where('type', 'sale')->avg('total_ttc') ?? 0;
        foreach ($allInvoices as $invoice) {
            if ($invoice->type !== 'cancelled') {
                continue;
            }
            // Flag cancellations whose original amount > 3× the average sale
            if ($avgTtc > 0 && abs((float) $invoice->total_ttc) > ($avgTtc * 3)) {
                $anomalies[] = [
                    'journal_id'  => $journal->id,
                    'invoice_id'  => $invoice->id,
                    'severity'    => 'warning',
                    'type'        => 'suspicious_cancellation',
                    'description' => "Annulation suspecte de montant élevé : {$invoice->invoice_no} — TTC = {$invoice->total_ttc} CDF (moy. vente = " . round($avgTtc, 0) . " CDF).",
                    'is_resolved' => false,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }
        }

        if (!empty($anomalies)) {
            Anomaly::insert($anomalies);
        }
    }

    // ──────────────────────────────────────────────────────────────────────
    // ITEM PARSER
    // ──────────────────────────────────────────────────────────────────────

    private function parseAndSaveItems(array $block, int $invoiceId): void
    {
        $itemLines  = [];
        $inItems    = false;

        foreach ($block as $line) {
            if (str_contains($line, '#Nom')) {
                $inItems = true;
                continue;
            }
            if ($inItems) {
                if (str_starts_with($line, '- - -')) {
                    break;
                }
                $itemLines[] = $line;
            }
        }

        $currentGroup = [];
        foreach ($itemLines as $line) {
            $currentGroup[] = $line;
            if (str_contains($line, '*')) {
                $item = $this->parseItemGroup($currentGroup);
                if ($item && isset($item['name'])) {
                    InvoiceItem::create([
                        'invoice_id' => $invoiceId,
                        'item_index' => $item['index'] ?? 1,
                        'name'       => $item['name'],
                        'qty'        => $item['qty'] ?? 1,
                        'pu'         => $item['pu'] ?? 0,
                        'total'      => $item['total'] ?? 0,
                        'tax_group'  => $item['tax_group'] ?? null,
                    ]);
                }
                $currentGroup = [];
            }
        }
    }

    private function parseItemGroup(array $lines): ?array
    {
        $index = null; $name = null; $taxGroup = null; $qty = null; $pu = null; $total = null;

        foreach ($lines as $line) {
            $sline = trim($line);
            if (!$sline) {
                continue;
            }
            if (str_contains($sline, '*')) {
                $parts    = explode('*', $sline, 2);
                $totalStr = str_replace([' ', ','], ['', '.'], trim($parts[1]));
                $total    = is_numeric($totalStr) ? (float) $totalStr : null;
                $candidate = trim(str_replace(['…', '.'], '', $parts[0]));
                if ($candidate && !$name) {
                    [$index, $name, $taxGroup] = $this->extractNameFromCandidate($candidate);
                }
            }
            if (preg_match('/(-?\d+)\s*x\s*([\d\s,]+)/u', $sline, $m)) {
                $qty = (int) $m[1];
                $pu  = (float) str_replace([' ', ','], ['', '.'], trim($m[2]));
                $textBefore = trim(substr($sline, 0, strpos($sline, $m[0])));
                if ($textBefore && !$name) {
                    [$index, $name, $taxGroup] = $this->extractNameFromCandidate($textBefore);
                }
            } elseif (!str_contains($sline, '*') && !$name) {
                [$index, $name, $taxGroup] = $this->extractNameFromCandidate($sline);
            }
        }

        if (!$name) {
            return null;
        }
        return compact('index', 'name', 'taxGroup', 'qty', 'pu', 'total');
    }

    private function extractNameFromCandidate(string $candidate): array
    {
        $index = null; $name = null; $taxGroup = null;
        $candidate = trim($candidate);

        if (preg_match('/^(\d+)\s+(.+)$/u', $candidate, $m)) {
            $index    = (int) $m[1];
            $namePart = trim($m[2]);
        } else {
            $namePart = $candidate;
        }
        if (preg_match('/\(([A-Z])\)$/u', $namePart, $m)) {
            $taxGroup = $m[1];
            $name     = trim(substr($namePart, 0, -strlen($m[0])));
        } else {
            $name = $namePart;
        }
        return [$index, $name ?: null, $taxGroup];
    }

    // ──────────────────────────────────────────────────────────────────────
    // TOTAL PARSERS
    // ──────────────────────────────────────────────────────────────────────

    private function parseTotalTtc(array $block): array
    {
        foreach ($block as $i => $line) {
            $cleaned = str_replace([' ', "\t"], '', $line);
            if (str_contains(strtoupper($cleaned), 'TOTALTTC')) {
                $next = isset($block[$i + 1]) ? str_replace([' ', "\t"], '', $block[$i + 1]) : '';
                foreach (['CDF', 'USD', 'EUR'] as $cur) {
                    foreach ([$cleaned, $next] as $haystack) {
                        if (str_contains($haystack, $cur)) {
                            if (preg_match('/' . $cur . '([-\d,\.]+)/', $haystack, $m)) {
                                return [(float) str_replace(',', '.', $m[1]), $cur];
                            }
                        }
                    }
                }
                break;
            }
        }
        return [null, null];
    }

    private function parseTaxTotals(array $block): array
    {
        $ht = null; $tva = null;
        foreach ($block as $i => $line) {
            $cleaned = str_replace([' ', "\t"], '', $line);
            if (str_contains(strtoupper($cleaned), 'TOTALH.T.') && $ht === null) {
                $target = str_contains($line, '*') ? $line : ($block[$i + 1] ?? '');
                if (preg_match('/\*\s*([-\d\s,.]+)/', $target, $m)) {
                    $ht = (float) str_replace([' ', ','], ['', '.'], trim($m[1]));
                }
            } elseif (str_contains(strtoupper($cleaned), 'TOTALTVA') && $tva === null) {
                $target = str_contains($line, '*') ? $line : ($block[$i + 1] ?? '');
                if (preg_match('/\*\s*([-\d\s,.]+)/', $target, $m)) {
                    $tva = (float) str_replace([' ', ','], ['', '.'], trim($m[1]));
                }
            }
        }
        return [$ht, $tva];
    }

    // ──────────────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────────────

    private function normalize(string $text): string
    {
        return strtoupper(preg_replace('/[\s\-\*:=#,\.\'\?…]/', '', $text));
    }

    private function extractField(string $text, string $label): ?string
    {
        if (preg_match('/' . preg_quote($label, '/') . '\s*:\s*([^\n\r]+)/i', $text, $m)) {
            return trim($m[1]);
        }
        return null;
    }

    private function detectPaymentMode(string $text): ?string
    {
        $norm = strtoupper($text);
        if (str_contains($norm, 'MOBILE MONEY') || str_contains($norm, 'MPESA') || str_contains($norm, 'AIRTEL')) {
            return 'MOBILE_MONEY';
        }
        if (str_contains($norm, 'BANQUE') || str_contains($norm, 'CARTE') || str_contains($norm, 'VISA')) {
            return 'BANQUE';
        }
        if (str_contains($norm, 'ESPÈCES') || str_contains($norm, 'ESPECES')) {
            return 'ESPECES';
        }
        return null;
    }

    private function extractIsf(array $invoices): ?string
    {
        foreach ($invoices as $invoice) {
            if ($invoice->raw_text && preg_match('/ISF:\s*(\S+)/i', $invoice->raw_text, $m)) {
                return trim($m[1]);
            }
        }
        return null;
    }
}
