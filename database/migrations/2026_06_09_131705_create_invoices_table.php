<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_no')->index();
            $table->unsignedInteger('serial_number')->index();
            $table->unsignedInteger('z_number')->index();
            $table->datetime('date_time')->nullable()->index();
            $table->string('buyer_name')->nullable();
            $table->string('buyer_id')->nullable();
            $table->string('buyer_type', 5)->nullable();
            $table->string('vendeur')->nullable();
            $table->decimal('total_ttc', 18, 2)->default(0);
            $table->decimal('total_ht', 18, 2)->default(0);
            $table->decimal('total_tva', 18, 2)->default(0);
            $table->string('currency', 5)->default('CDF');
            $table->enum('type', ['sale', 'credit_note', 'cancelled'])->default('sale')->index();
            $table->string('code_def')->nullable();
            $table->string('compteur_brut')->nullable();
            $table->boolean('has_mcf_error')->default(false)->index();
            $table->string('mcf_error_message')->nullable();
            $table->string('original_ref_code')->nullable();
            $table->string('payment_mode')->nullable();
            $table->mediumText('raw_text')->nullable();
            $table->timestamps();

            $table->index(['journal_id', 'type']);
            $table->index(['z_number', 'serial_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
