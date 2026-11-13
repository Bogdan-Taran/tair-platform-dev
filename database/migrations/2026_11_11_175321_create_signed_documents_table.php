<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signed_documents', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->foreignId('document_template_id')->constrained()->restrictOnDelete();
            $t->foreignId('child_id')->nullable()->constrained()->restrictOnDelete();
            $t->foreignId('contract_id')->nullable()->constrained()->restrictOnDelete();
            $t->string('subject_key');
            $t->string('kind');
            $t->string('title');
            $t->string('version');
            $t->longText('snapshot');
            $t->string('checksum', 64);
            $t->string('confirmation_method');
            $t->string('ip_address', 45);
            $t->timestamp('signed_at');
            $t->timestamp('expires_at')->nullable();
            $t->timestamp('revoked_at')->nullable();
            $t->string('revocation_reason', 1000)->nullable();
            $t->timestamps();
            $t->unique(['user_id', 'document_template_id', 'subject_key'], 'signed_documents_once');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signed_documents');
    }
};
