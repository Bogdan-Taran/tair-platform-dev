<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_templates', function (Blueprint $t) {
            $t->id();
            $t->string('kind');
            $t->string('version');
            $t->string('title');
            $t->longText('body');
            $t->unsignedInteger('valid_days')->nullable();
            $t->timestamp('published_at')->nullable();
            $t->timestamps();
            $t->unique(['kind', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_templates');
    }
};
