<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stage_service_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('status_type');
            $table->foreignId('service_catalog_id')->constrained('service_catalog')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['status_type', 'service_catalog_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stage_service_mappings');
    }
};
