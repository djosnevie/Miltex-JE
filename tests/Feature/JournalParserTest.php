<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Device;
use App\Models\PointOfSale;
use App\Models\Tenant;
use App\Models\Journal;
use App\Models\Invoice;
use App\Models\Anomaly;
use App\Services\JournalParserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalParserTest extends TestCase
{
    use RefreshDatabase;

    private Device $device;
    private JournalParserService $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = $this->app->make(JournalParserService::class);

        // Seed basic architecture models
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'subdomain' => 'test',
        ]);

        $company = Company::create([
            'tenant_id' => $tenant->id,
            'name' => 'M I L T E X   S A R L',
            'nif' => 'A2609384F',
        ]);

        $pos = PointOfSale::create([
            'company_id' => $company->id,
            'name' => 'Dépôt Test',
        ]);

        $this->device = Device::create([
            'point_of_sale_id' => $pos->id,
            'nid' => 'IC02000193-1',
            'isf' => '102030405',
        ]);
    }

    public function test_it_can_parse_and_import_example_journal_file(): void
    {
        $filePath = base_path('JE_IC02000193-1_1532.txt');

        $this->assertFileExists($filePath);

        $journal = $this->parser->parseFile($filePath, basename($filePath), $this->device->id);

        // Assert journal was created and metadata is correct
        $this->assertNotNull($journal);
        $this->assertInstanceOf(Journal::class, $journal);
        $this->assertEquals($this->device->id, $journal->device_id);
        $this->assertEquals(basename($filePath), $journal->filename);

        // Assert numbers and invoices
        $this->assertGreaterThan(0, $journal->total_invoices);
        
        $invoicesCount = Invoice::where('journal_id', $journal->id)->count();
        $this->assertEquals($journal->total_invoices + $journal->total_credits + $journal->total_cancelled, $invoicesCount);

        // Assert that invoices items are parsed
        $invoice = Invoice::where('journal_id', $journal->id)->first();
        $this->assertNotNull($invoice);
        $this->assertGreaterThan(0, $invoice->items()->count());

        // Verify that sums match
        $sumTtc = Invoice::where('journal_id', $journal->id)->where('type', 'sale')->sum('total_ttc');
        $this->assertEquals($journal->total_ttc, $sumTtc);

        $sumHt = Invoice::where('journal_id', $journal->id)->where('type', 'sale')->sum('total_ht');
        $this->assertEquals($journal->total_ht, $sumHt);

        $sumTva = Invoice::where('journal_id', $journal->id)->where('type', 'sale')->sum('total_tva');
        $this->assertEquals($journal->total_tva, $sumTva);

        // Verify that anomalies were detected
        $anomaliesCount = Anomaly::where('journal_id', $journal->id)->count();
        $this->assertGreaterThan(0, $anomaliesCount);
    }
}
