<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_splits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('split_type', 20);
            $table->unsignedBigInteger('custom_amount')->nullable();
            $table->unsignedInteger('percentage')->nullable();
            $table->unsignedBigInteger('share_amount');

            $table->unique(['expense_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_splits');
    }
};
