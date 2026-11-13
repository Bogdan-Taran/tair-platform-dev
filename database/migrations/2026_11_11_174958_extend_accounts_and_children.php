<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('patronymic')->nullable()->change();
            $table->string('avatar_path')->nullable();
            $table->string('account_type')->default('parent');
            $table->timestamp('blocked_at')->nullable();
            $table->unsignedInteger('session_version')->default(0);
            $table->timestamp('terms_accepted_at')->nullable();
            $table->string('terms_version')->nullable();
        });
        Schema::table('children', function (Blueprint $table) {
            $table->string('patronymic')->nullable()->change();
            $table->timestamp('archived_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['avatar_path', 'account_type', 'blocked_at', 'session_version', 'terms_accepted_at', 'terms_version']));
        Schema::table('children', fn (Blueprint $table) => $table->dropColumn('archived_at'));
    }
};
