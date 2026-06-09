<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Services\JournalParserService;
use Illuminate\Console\Command;

class ScanJournals extends Command
{
    protected $signature = 'journals:scan
                            {--file=       : Chemin vers un fichier spécifique à importer}
                            {--device=     : NID du DEF (ex: IC02000193-1), requis si --file est précisé}
                            {--dir=        : Répertoire à scanner (par défaut: storage/app/journals)}';

    protected $description = 'Importer un ou plusieurs fichiers journaux électroniques (.txt) dans la base de données';

    public function handle(JournalParserService $parser): int
    {
        $specificFile = $this->option('file');
        $deviceNid    = $this->option('device');
        $dir          = $this->option('dir') ?? \Illuminate\Support\Facades\Storage::disk('local')->path(config('eaje.journals_path', 'journals'));

        // ── Mode fichier unique ────────────────────────────────────────────
        if ($specificFile) {
            if (!file_exists($specificFile)) {
                $this->error("Fichier introuvable : $specificFile");
                return self::FAILURE;
            }
            $device = $this->resolveDevice($deviceNid, $specificFile);
            if (!$device) {
                return self::FAILURE;
            }
            $this->importFile($parser, $specificFile, basename($specificFile), $device->id);
            return self::SUCCESS;
        }

        // ── Mode répertoire ────────────────────────────────────────────────
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
            $this->info("Répertoire créé : $dir");
        }

        $files = glob($dir . '/*.txt');
        if (empty($files)) {
            $this->warn("Aucun fichier .txt trouvé dans : $dir");
            return self::SUCCESS;
        }

        $imported = 0;
        $skipped  = 0;
        $errors   = 0;

        foreach ($files as $filePath) {
            // Try to detect device from filename (NID-based naming convention)
            $device = $this->resolveDeviceFromFilename(basename($filePath));
            if (!$device) {
                $this->warn("  ⚠  Aucun DEF trouvé pour : " . basename($filePath) . " — ignoré");
                $skipped++;
                continue;
            }
            $result = $this->importFile($parser, $filePath, basename($filePath), $device->id);
            match ($result) {
                'imported' => $imported++,
                'skipped'  => $skipped++,
                default    => $errors++,
            };
        }

        $this->info("Terminé — Importés : $imported | Ignorés : $skipped | Erreurs : $errors");
        return self::SUCCESS;
    }

    // ──────────────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────────────

    private function importFile(JournalParserService $parser, string $filePath, string $originalName, int $deviceId): string
    {
        $this->line("  Traitement : <info>$originalName</info>");
        try {
            $journal = $parser->parseFile($filePath, $originalName, $deviceId);
            $this->info(sprintf(
                "  ✓ Importé : %d ventes | %d avoirs | %d annulées — CA TTC : %s %s",
                $journal->total_invoices,
                $journal->total_credits,
                $journal->total_cancelled,
                number_format($journal->total_ttc, 2, ',', ' '),
                $journal->currency
            ));
            return 'imported';
        } catch (\RuntimeException $e) {
            if (str_contains($e->getMessage(), 'déjà été importé') || str_contains($e->getMessage(), 'filename')) {
                $this->warn("  → Déjà importé : $originalName");
                return 'skipped';
            }
            $this->error("  ✗ Erreur : " . $e->getMessage());
            return 'error';
        } catch (\Exception $e) {
            $this->error("  ✗ Erreur inattendue : " . $e->getMessage());
            return 'error';
        }
    }

    private function resolveDevice(?string $nid, string $filePath): ?Device
    {
        if ($nid) {
            $device = Device::where('nid', $nid)->first();
            if (!$device) {
                $this->error("Aucun DEF trouvé avec le NID : $nid");
                return null;
            }
            return $device;
        }
        return $this->resolveDeviceFromFilename(basename($filePath));
    }

    /**
     * Les fichiers journaux sont nommés par convention: JE_<NID>_<seq>.txt
     * Ex: JE_IC02000193-1_1532.txt → NID = IC02000193-1
     */
    private function resolveDeviceFromFilename(string $filename): ?Device
    {
        // Pattern: JE_<NID>_<anything>.txt
        if (preg_match('/^JE_([A-Z0-9\-]+)_/i', $filename, $m)) {
            return Device::where('nid', $m[1])->first();
        }
        return null;
    }
}
