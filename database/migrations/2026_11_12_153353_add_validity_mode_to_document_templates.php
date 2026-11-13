<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_templates', fn (Blueprint $t) => $t->string('validity_mode')->default('days'));
    }

    public function down(): void
    {
        Schema::table('document_templates', fn (Blueprint $t) => $t->dropColumn('validity_mode'));
    }
};
