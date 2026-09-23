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
        Schema::table('groups', function (Blueprint $table) {
            $table->string('avatar_path')->nullable()->after('description');
            $table->string('category', 50)->default('Lainnya')->after('avatar_path');
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn(['avatar_path', 'category']);
        });
    }
};
