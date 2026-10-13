<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->nullable()->unique()->after('name');
            $table->text('bio')->nullable()->after('email');
            $table->string('phone', 30)->nullable()->after('bio');
            $table->string('bank_name', 50)->nullable()->after('phone');
            $table->string('bank_account', 100)->nullable()->after('bank_name');
            $table->string('avatar_path')->nullable()->after('bank_account');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'bio',
                'phone',
                'bank_name',
                'bank_account',
                'avatar_path',
            ]);
        });
    }
};
