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
        Schema::create('wallet_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_wallet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 10); // in, out
            $table->string('category', 50); // iuran, konsumsi, sewa, lainnya
            $table->integer('amount');
            $table->text('description')->nullable();
            $table->string('receipt_photo')->nullable();
            $table->string('status', 20)->default('approved'); // pending, approved, rejected
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_entries');
    }
};
