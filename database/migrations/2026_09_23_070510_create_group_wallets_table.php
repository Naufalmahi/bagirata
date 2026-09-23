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
        Schema::create('group_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->string('name')->default('Kas Utama');
            $table->integer('balance')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_wallets');
    }
};
