<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Device;
use App\Models\PointOfSale;
use App\Models\Tenant;
use App\Models\Journal;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Services\JournalParserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiTenancyAndConsistencyTest extends TestCase
{
    use RefreshDatabase;

    private JournalParserService $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = $this->app->make(JournalParserService::class);
    }

    public function test_multi_tenancy_data_isolation(): void
    {
        // Tenant A Setup
        $tenantA = Tenant::create(['name' => 'Tenant A', 'subdomain' => 'tenant-a']);
        $companyA = Company::create(['tenant_id' => $tenantA->id, 'name' => 'MILTEX SARL A', 'nif' => 'NIF-A']);
        $posA = PointOfSale::create(['company_id' => $companyA->id, 'name' => 'POS A']);
        $deviceA = Device::create(['point_of_sale_id' => $posA->id, 'nid' => 'IC-DEVICE-A']);

        // Tenant B Setup
        $tenantB = Tenant::create(['name' => 'Tenant B', 'subdomain' => 'tenant-b']);
        $companyB = Company::create(['tenant_id' => $tenantB->id, 'name' => 'MILTEX SARL B', 'nif' => 'NIF-B']);
        $posB = PointOfSale::create(['company_id' => $companyB->id, 'name' => 'POS B']);
        $deviceB = Device::create(['point_of_sale_id' => $posB->id, 'nid' => 'IC-DEVICE-B']);

        // Create a Journal for Tenant A
        $journalA = Journal::create([
            'device_id' => $deviceA->id,
            'filename' => 'JE_A.txt',
            'original_name' => 'JE_A.txt',
            'file_size' => 1024,
            'total_invoices' => 10,
        ]);

        // Create a Journal for Tenant B
        $journalB = Journal::create([
            'device_id' => $deviceB->id,
            'filename' => 'JE_B.txt',
            'original_name' => 'JE_B.txt',
            'file_size' => 2048,
            'total_invoices' => 20,
        ]);

        // Validate isolation: Querying journals through Tenant A only returns Tenant A's journals
        $tenantAJournals = Journal::whereIn('device_id', function ($query) use ($tenantA) {
            $query->select('devices.id')
                ->from('devices')
                ->join('points_of_sale', 'devices.point_of_sale_id', '=', 'points_of_sale.id')
                ->join('companies', 'points_of_sale.company_id', '=', 'companies.id')
                ->where('companies.tenant_id', $tenantA->id);
        })->get();

        $this->assertCount(1, $tenantAJournals);
        $this->assertEquals('JE_A.txt', $tenantAJournals->first()->filename);

        // Validate isolation: Querying journals through Tenant B only returns Tenant B's journals
        $tenantBJournals = Journal::whereIn('device_id', function ($query) use ($tenantB) {
            $query->select('devices.id')
                ->from('devices')
                ->join('points_of_sale', 'devices.point_of_sale_id', '=', 'points_of_sale.id')
                ->join('companies', 'points_of_sale.company_id', '=', 'companies.id')
                ->where('companies.tenant_id', $tenantB->id);
        })->get();

        $this->assertCount(1, $tenantBJournals);
        $this->assertEquals('JE_B.txt', $tenantBJournals->first()->filename);
    }

    public function test_arithmetic_consistency_of_invoices_and_items(): void
    {
        // Setup Tenant, Company, POS, and Device
        $tenant = Tenant::create(['name' => 'Test Tenant', 'subdomain' => 'test']);
        $company = Company::create(['tenant_id' => $tenant->id, 'name' => 'M I L T E X   S A R L', 'nif' => 'A2609384F']);
        $pos = PointOfSale::create(['company_id' => $company->id, 'name' => 'Dépôt Test']);
        $device = Device::create(['point_of_sale_id' => $pos->id, 'nid' => 'IC02000193-1']);

        $filePath = base_path('JE_IC02000193-1_1532.txt');
        $journal = $this->parser->parseFile($filePath, basename($filePath), $device->id);

        // 1. Check Invoice totals equal the sum of their items
        $invoices = Invoice::with('items')->where('journal_id', $journal->id)->get();
        foreach ($invoices as $invoice) {
            if ($invoice->type === 'sale') {
                $itemsSum = $invoice->items->sum('total');
                // The item totals in the electronic journal should match the invoice's total TTC (or total HT + TVA)
                // Let's assert they are close or identical
                $this->assertEqualsWithDelta($invoice->total_ttc, $itemsSum, 0.05, "Invoice {$invoice->invoice_no} items total mismatch.");
            }
        }

        // 2. Check Journal totals match the sum of its sales invoices
        $salesInvoices = $invoices->where('type', 'sale');
        $this->assertEqualsWithDelta($journal->total_ttc, $salesInvoices->sum('total_ttc'), 0.05);
        $this->assertEqualsWithDelta($journal->total_ht, $salesInvoices->sum('total_ht'), 0.05);
        $this->assertEqualsWithDelta($journal->total_tva, $salesInvoices->sum('total_tva'), 0.05);
    }

    public function test_it_can_download_detailed_tva_excel_export(): void
    {
        $tenant = Tenant::create(['name' => 'Test Tenant', 'subdomain' => 'test']);
        $company = Company::create(['tenant_id' => $tenant->id, 'name' => 'M I L T E X   S A R L', 'nif' => 'A2609384F']);
        $pos = PointOfSale::create(['company_id' => $company->id, 'name' => 'Dépôt Test']);
        $device = Device::create(['point_of_sale_id' => $pos->id, 'nid' => 'IC02000193-1']);

        $filePath = base_path('JE_IC02000193-1_1532.txt');
        $journal = $this->parser->parseFile($filePath, basename($filePath), $device->id);

        $response = $this->get(route('export.tva.excel', $journal->id));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_it_can_download_daily_tva_pdf_export(): void
    {
        $tenant = Tenant::create(['name' => 'Test Tenant', 'subdomain' => 'test']);
        $company = Company::create(['tenant_id' => $tenant->id, 'name' => 'M I L T E X   S A R L', 'nif' => 'A2609384F']);
        $pos = PointOfSale::create(['company_id' => $company->id, 'name' => 'Dépôt Test']);
        $device = Device::create(['point_of_sale_id' => $pos->id, 'nid' => 'IC02000193-1']);

        $filePath = base_path('JE_IC02000193-1_1532.txt');
        $journal = $this->parser->parseFile($filePath, basename($filePath), $device->id);

        $response = $this->get(route('export.tva.daily.pdf', $journal->id));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
