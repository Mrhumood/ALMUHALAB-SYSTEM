<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('service_requests')) {
            // convert legacy 'open' statuses to the new 'New' state
            DB::table('service_requests')->where('status', 'open')->update(['status' => 'New']);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('service_requests')) {
            // revert 'New' back to legacy 'open' if needed
            DB::table('service_requests')->where('status', 'New')->update(['status' => 'open']);
        }
    }
};
