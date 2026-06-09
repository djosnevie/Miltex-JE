<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->string('serial_number')->nullable()->after('nid');
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active')->after('firmware_version');
            $table->string('description')->nullable()->after('status');
            $table->timestamp('registered_at')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['serial_number', 'status', 'description', 'registered_at']);
        });
    }
};
