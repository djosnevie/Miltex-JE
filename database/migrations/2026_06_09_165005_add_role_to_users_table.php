<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'admin', 'analyst'])
                  ->default('analyst')
                  ->after('email');
            $table->foreignId('tenant_id')
                  ->nullable()
                  ->constrained('tenants')
                  ->nullOnDelete()
                  ->after('role');
            $table->boolean('is_active')
                  ->default(true)
                  ->after('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn(['role', 'tenant_id', 'is_active']);
        });
    }
};
