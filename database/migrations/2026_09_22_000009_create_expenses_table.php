<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nongkrong_session_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->unsignedBigInteger('amount');
            $table->foreignId('paid_by_user_id')->constrained('users');
            $table->string('category', 30)->index();
            $table->text('note')->nullable();
            $table->string('receipt_photo', 255)->nullable();
            $table->string('discount_type', 10)->default('fixed');
            $table->unsignedInteger('discount_value')->default(0);
            $table->unsignedInteger('service_rate')->default(0);
            $table->unsignedInteger('tax_rate')->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
