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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('item_index')->default(1);
            $table->string('name');
            $table->integer('qty')->default(1);
            $table->decimal('pu', 18, 2)->default(0);
            $table->decimal('total', 18, 2)->default(0);
            $table->string('tax_group', 2)->nullable();
            $table->timestamps();

            $table->index(['invoice_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
