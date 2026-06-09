<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('points_of_sale', function (Blueprint $table) {
            $table->string('address')->nullable()->after('city');
            $table->string('phone', 30)->nullable()->after('address');
            $table->string('email')->nullable()->after('phone');
            $table->text('description')->nullable()->after('email');
            $table->boolean('is_active')->default(true)->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('points_of_sale', function (Blueprint $table) {
            $table->dropColumn(['address', 'phone', 'email', 'description', 'is_active']);
        });
    }
};
