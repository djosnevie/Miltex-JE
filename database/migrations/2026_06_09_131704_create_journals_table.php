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
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->string('filename')->unique();
            $table->string('original_name');
            $table->unsignedBigInteger('file_size');
            $table->timestamp('parsed_at')->nullable();
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->unsignedInteger('total_invoices')->default(0);
            $table->unsignedInteger('total_credits')->default(0);
            $table->unsignedInteger('total_cancelled')->default(0);
            $table->decimal('total_ttc', 18, 2)->default(0);
            $table->decimal('total_ht', 18, 2)->default(0);
            $table->decimal('total_tva', 18, 2)->default(0);
            $table->string('currency', 5)->default('CDF');
            $table->string('file_hash', 64)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
