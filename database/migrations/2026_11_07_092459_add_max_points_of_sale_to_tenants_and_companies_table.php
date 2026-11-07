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
        Schema::table('tenants', function (Blueprint $table) {
            $table->unsignedInteger('max_points_of_sale')->default(5)->after('status');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->unsignedInteger('max_points_of_sale')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('max_points_of_sale');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('max_points_of_sale');
        });
    }
};
