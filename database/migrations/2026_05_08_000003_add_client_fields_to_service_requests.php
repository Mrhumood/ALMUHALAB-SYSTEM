<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->string('client_name', 150)->nullable()->after('request_number');
            $table->string('client_phone_code', 10)->nullable()->after('client_name');
            $table->string('client_phone', 30)->nullable()->after('client_phone_code');
            $table->string('client_email', 150)->nullable()->after('client_phone');
            $table->json('companions_data')->nullable()->after('companions_count');
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn(['client_name', 'client_phone_code', 'client_phone', 'client_email', 'companions_data']);
        });
    }
};
