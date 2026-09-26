<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallet_entries', function (Blueprint $table) {
            $table->foreignId('reviewed_by')->nullable()->after('approved_by')->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->text('review_note')->nullable()->after('reviewed_at');
        });
    }

    public function down(): void
    {
        Schema::table('wallet_entries', function (Blueprint $table) {
            $table->dropColumn(['reviewed_by', 'reviewed_at', 'review_note']);
        });
    }
};
