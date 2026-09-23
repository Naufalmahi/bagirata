<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debt_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debt_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('amount');
            $table->string('method', 20);
            $table->text('note')->nullable();
            $table->string('proof_photo')->nullable();
            $table->string('status', 12)->default('pending')->index();
            $table->text('review_note')->nullable();
            $table->foreignId('reported_by_user_id')->constrained('users');
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['debt_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debt_payments');
    }
};
