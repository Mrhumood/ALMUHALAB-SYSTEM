<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('whatsapp_number', 30)->nullable()->after('phone_number');
            $table->boolean('notify_email')->default(true)->after('whatsapp_number');
            $table->boolean('notify_whatsapp')->default(false)->after('notify_email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_number', 'notify_email', 'notify_whatsapp']);
        });
    }
};
