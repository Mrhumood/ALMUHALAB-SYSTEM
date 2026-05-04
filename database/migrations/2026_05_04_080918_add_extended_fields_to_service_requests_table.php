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
        Schema::table('service_requests', function (Blueprint $table) {
            $table->string('client_country')->nullable()->after('status');
            $table->string('destination_country')->nullable()->after('client_country');
            $table->string('destination_city')->nullable()->after('destination_country');
            $table->date('travel_date_start')->nullable()->after('destination_city');
            $table->date('travel_date_end')->nullable()->after('travel_date_start');
            $table->unsignedSmallInteger('companions_count')->default(0)->after('travel_date_end');
            $table->text('additional_notes')->nullable()->after('companions_count');
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn([
                'client_country', 'destination_country', 'destination_city',
                'travel_date_start', 'travel_date_end',
                'companions_count', 'additional_notes',
            ]);
        });
    }
};
