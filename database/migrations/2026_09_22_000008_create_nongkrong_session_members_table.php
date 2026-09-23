<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nongkrong_session_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nongkrong_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();

            $table->unique(['nongkrong_session_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nongkrong_session_members');
    }
};
