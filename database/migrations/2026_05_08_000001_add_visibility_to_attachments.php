<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            // visibility column already exists (all/employee/admin)
            // we only add the required_permission for fine-grained "admin" access
            $table->string('required_permission', 50)->nullable()->after('visibility');
        });
    }

    public function down(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropColumn('required_permission');
        });
    }
};
